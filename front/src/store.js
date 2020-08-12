import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

let serverurl = '//localhost/server'


export default new Vuex.Store({
  state: {
    windowSize: {
      x: 0,
      y: 0,
    },
    reapply: {
      index: 0,
      valid: false,
    },
    chat: {
      state: false,
      room: "",
    },
    options: {
      home_background_color: {
        name: "grey lighten-4", code: '#F5F5F5'
      },
    },
    snackbar: {
      on: false,
      text: '',
      type: 'primary',
    },

    all: {
      users: {},
      receipts: {},
      settings: {},
      formats: {},
      chats: {},
      id: '',

    },
    ajaxurl: serverurl + '/ajax.php',
    uploadsurl: {
      'format': serverurl + '/format-uploads/',
      'receipt': serverurl + '/receipt-uploads/',
      'dummy': serverurl + '/uploads-dummy/',
    },
    masks: {
      call: "### #### ####",
      fax: "### ### ####",
      companyRegisterNumber: "### ## #####",
      regidentRegisterNumber: "###### #######"
    },
    valid: {
      min: function (v, min) {
        if (v) return v.length >= min || "길이가 " + min + " 보다 길어야 합니다.";
        else return true;
      },
      max: function (v, max) {
        if (v)
          return v.length <= max || "길이가 " + max + " 보다 짧아야 합니다.";
        else return true;
      },
      size_min: function (v, min) {
        if (v) return v >= min || min + " 보다 커야 합니다.";
        else return true;
      },
      size_max: function (v, max) {
        if (v) return v <= max || max + " 보다 작아야 합니다.";
        else return true;
      },
      required: function (v) {
        return !!v || "필수로 입력해야 합니다.";
      },
      filter: function (v, types, adds) {
        if (!v) return true;
        if (!adds) adds = [];
        const number = "0-9";
        const english = "a-z";
        let regex = "[^";
        let errormsg = "";
        let errormsgs = [];
        for (let type of types) {
          if (type === "number") {
            errormsgs.push("숫자");
            regex += number;
          } else if (type === "english") {
            errormsgs.push("영문");
            regex += english;
          }
        }
        for (let add of adds) {
          errormsgs.push(add);
          regex += add;
        }

        regex += "]";
        for (let msg of errormsgs) {
          errormsg += msg + " ";
        }
        errormsg += "만 입력 가능합니다.";
        return !v.match(regex) || errormsg;
      }
    },
    receipt: {
      selected: '',
      receipts: {
        "취득신고 내국인": {
          category: "피보험자관리",
          name: "취득신고 내국인",
          englishName: "Acquisition Notification",
          src: require("@/assets/landscapeIcons/home.png"),
          doc: "내국인 대상 4대보험 신청 페이지 입니다.",
        },
        "취득신고 외국인": {
          category: "피보험자관리",
          name: "취득신고 외국인",
          englishName: "Acquisition Notification Foreigner",
          src: require("@/assets/landscapeIcons/home-1.png"),
          doc: "외국인 대상 4대보험 신청 페이지 입니다.<br>의무가입대상이 아닌 외국인 노동자는 기타신고를 통해 신고해주세요"
        },
        "취득신고 일용직": {
          category: "피보험자관리",
          name: "취득신고 일용직",
          englishName: "Acquisition Notification Daily Labor",
          src: require("@/assets/landscapeIcons/hills.png"),
          doc: "일용직 대상 4대보험 신청 페이지 입니다.",
        },
        "상실신고": {
          category: "피보험자관리",
          name: "상실신고",
          englishName: "Report of lose",
          src: require("@/assets/landscapeIcons/cape.png"),
          doc: "4대보험 상실신고 신청페이지 입니다.",
        },

        "기타신고": {
          category: "기타관리",
          name: "기타신고",
          englishName: "Report of other",
          src: require("@/assets/landscapeIcons/beach.png"),
          doc: "기타신고서 작성페이지 입니다.<br>*휴직신고, 외국인 임의가입, 보수변경, 피부양자 추가, 일자리안정자금 신고",
        },
        "사업장 변경신고": {
          category: "사업장관리",
          name: "사업장 변경신고",
          englishName: "Report change of business place",
          doc: "사업장 변경신고 신청페이지 입니다..<br>*대표자정보, 사업장명칭, 사업장소재지, 사업자등록번호, 우편물수령지주소추가, 전화또는팩스추가,건설업개시신고사업자변경",
          src: require("@/assets/landscapeIcons/waterfall-1.png")
        },
        "사업장 위탁신고": {
          category: "사업장관리",
          name: "사업장 위탁신고",
          englishName: "Report of work place consignment",
          src: require("@/assets/landscapeIcons/waterfall.png"),
          doc: "사업장 위탁신고 신청페이지 입니다.",
        },
        "폐업 및 수임해지": {
          category: "사업장관리",
          name: "폐업 및 수임해지",
          englishName: "Report of business termination",
          src: require("@/assets/landscapeIcons/mountains-1.png"),
          doc: "폐업 및 수임해지 신청페이지 입니다.",
        },
      },
    },
    kinds: {
      receipts: ['사업장 변경신고', '폐업 및 수임해지', '사업장 위탁신고', '취득신고 내국인', '취득신고 외국인', '취득신고 일용직', '상실신고', '기타신고'],
      dependents: ['배우자', '자녀', '부모'],
      yesNo: ["예", "아니요"],
      terminate: ["사업장 폐업신고", "사무대행기관 수임해지"],
      another: ["휴직신고", "외국인 임의가입", "보수변경", "피부양자 추가", "일자리 안정자금 신고"],
      reason: ["폐업/도산", "근로자 없음", "휴업", "기타"],
      out: [
        "개인사정으로 인한 자진퇴사",
        "사업장 이전, 근로조건변동, 임금체불 등으로 자진퇴사",
        "폐업/도산",
        "경영상 필요/인원감축 등에 의한 퇴사",
        "근로자의 귀책사유에 의한 징계해고/권고사직",
        "청년",
        "계약만료, 공사종료",
        "고용보험비적용, 이중고용"
      ],
      change: [
        "대표자 정보",
        "사업자 명칭",
        "사업장 소재지",
        "사업자등록번호 변경",
        // "법인등록번호 변경",
        // "업종 변경",
        "우편물수령지주소 추가",
        "전화 또는 팩스번호 추가",
        "건설업 개시신고 사업장 정보변경"
      ],
      employmentType: ["정규직", "계약직", "사업주"],
      insuranceDivision: ["산재보험", "고용보험", "산재, 고용보험"],

      occupations: [
        "101   기획·전략·경영",
        "102   총무·법무·사무",
        "103   경리·출납·결산",
        "104   홍보·PR·사보",
        "107   비서·안내·수행원",
        "108   사무보조·문서작성",
        "113   인사·교육·노무",
        "114   회계·재무·세무·IR",
        "119   마케팅·광고·분석",
        "120   전시·컨벤션·세미나",
        "202   일반영업",
        "203   판매·매장관리",
        "205   IT·솔루션영업",
        "206   금융·보험영업",
        "207   광고영업",
        "208   기술영업",
        "209   영업기획·관리·지원",
        "210   TM·아웃바운드",
        "211   TM·인바운드",
        "212   고객센터·CS",
        "213   QA·CS강사·수퍼바이저",
        "301   금속·금형",
        "302   기계·기계설비",
        "303   화학·에너지",
        "304   섬유·의류·패션",
        "308   전기·전자·제어",
        "309   생산관리·품질관리",
        "314   반도체·디스플레이·LCD",
        "315   생산·제조·포장·조립",
        "316   비금속·요업·신소재",
        "317   바이오·제약·식품",
        "318   설계·CAD·CAM",
        "319   안경·렌즈·광학",
        "401   웹마스터·QA·테스터",
        "402   서버·네트워크·보안",
        "403   웹기획·PM",
        "404   웹개발",
        "405   게임·Game",
        "406   컨텐츠·사이트운영",
        "407   응용프로그램개발",
        "408   시스템개발",
        "409   ERP·시스템분석·설계",
        "410   통신·모바일",
        "411   하드웨어·소프트웨어",
        "412   웹디자인",
        "413   퍼블리싱·UI개발",
        "414   동영상·편집·코덱",
        "415   IT·디자인·컴퓨터교육",
        "416   데이터베이스·DBA",
        "417   인공지능(AI) ·빅데이터",
        "501   경영분석·컨설턴트",
        "502   증권·투자·펀드·외환",
        "504   헤드헌팅·노무·직업상담",
        "505   설문·통계·리서치",
        "509   외국어·번역·통역",
        "510   법률·특허·상표",
        "511   세무·회계·CPA",
        "512   채권·보험·보상·심사",
        "513   도서관사서",
        "518   연구소·R& D",
        "522   문화·예술·종교",
        "523   특수직",
        "524   임원·CEO",
        "601   교육기획·교재",
        "602   초중고·특수학교",
        "603   학습지·과외·방문",
        "604   유치원·보육",
        "605   전문직업·IT강사",
        "606   입시·보습·속셈학원",
        "609   학원관리·운영·상담",
        "610   대학교수·행정직",
        "611   외국어·어학원",
        "702   연예·엔터테인먼트",
        "703   카메라·조명·미술",
        "704   공연·무대·스텝",
        "705   작가·시나리오",
        "708   영화제작·배급",
        "709   광고·카피·CF",
        "710   기자",
        "711   방송연출·PD·감독",
        "712   진행·아나운서",
        "713   음악·음향·사운드",
        "714   인쇄·출판·편집",
        "715   사진·포토그라퍼",
        "807   중장년·고령인·실버",
        "808   장애인·국가유공자",
        "809   병역특례",
        "810   공무원",
        "812   사회복지·요양·봉사",
        "813   장교·군인·부사관",
        "901   토목·조경·도시·측량",
        "902   건축·인테리어·설계",
        "903   전기·소방·통신·설비",
        "904   환경·플랜트",
        "905   현장·시공·감리·공무",
        "906   안전·품질·검사·관리",
        "908   부동산·개발·경매·분양",
        "909   본사·관리·전산",
        "1001   물류·유통·운송",
        "1002   해외영업·무역영업",
        "1003   구매·자재·재고",
        "1004   납품·배송·택배",
        "1005   상품기획·MD",
        "1006   무역사무·수출입",
        "1007   운전·기사",
        "1008   중장비·화물",
        "1101   웨딩·행사·이벤트",
        "1102   안내·도우미·나레이터",
        "1103   보안·경호·안전",
        "1104   주차·세차·주유",
        "1105   AS·서비스·수리",
        "1107   외식·식음료",
        "1108   호텔·카지노·콘도",
        "1109   여행·관광·항공",
        "1110   레저·스포츠",
        "1111   미용·피부관리·애견",
        "1112   요리·제빵사·영양사",
        "1113   가사·청소·육아",
        "1201   그래픽디자인·CG",
        "1202   출판·편집디자인",
        "1203   제품·산업디자인",
        "1204   캐릭터·만화·애니",
        "1205   의류·패션·잡화디자인",
        "1207   디자인기타",
        "1208   전시·공간디자인",
        "1209   광고·시각디자인",
        "1301   의사·치과·한의사",
        "1302   수의사",
        "1303   약사",
        "1304   간호사",
        "1305   간호조무사",
        "1306   의료기사",
        "1307   사무·원무·코디",
        "1308   보험심사과",
        "1309   의료기타직"
      ],
      status_of_sojourns: [
        "0-0       체류자격없음    체류자격없음",
        "A-1       외교    외교",
        "A-2       공무    공무",
        "A-3-1     미군현역    주한미군(현역및예비역)",
        "A-3-2     미군군속    주한미군군속,초청계약자,가족",
        "A-3-99    기타협정    기타협정",
        "B-1       사증면제    사증면제",
        "B-2-1     일반무사증    일반무사증입국",
        "B-2-2     제주무사증    제주무사증입국",
        "C-1       일시취재    일시취재",
        "C-2-1     단기상용    단기상용",
        "C-2-2     우대기업    우대기업초청단기상용",
        "C-2-91    FTA상용    FTA상용방문자",
        "C-3       단기종합    자진출국단기종합",
        "C-3-1     단기일반    단기일반단체관광등(C-3-2)~일반관광(C-3-9)을제외 ",
        "C-3-2     단체관광등    단체관광등",
        "C-3-3     의료관광    의료관광",
        "C-3-4     일반상용    일반상용",
        "C-3-5     협정단기상용    협정상단기상용",
        "C-3-6     단기상용    우대기업초청단기상용",
        "C-3-7     도착관광    도착관광",
        "C-3-8     동포방문     외국국적동포3년간90일이내복수로국내출입국",
        "C-3-9     일반관광    C-3-2(단체관광등),의료관광(C-3-3)에포함안된일반관광 ",
        "C-3-M     의료관광    의료관광",
        "C-4       단기취업    단기취업",
        "D-1       문화예술    문화예술",
        "D-10      구직   구직",
        "D-10-1    구직활동    구직활동자",
        "D-10-2    기술창업활동    기술창업활동자",
        "D-2       유학    유학",
        "D-2-1     전문학사    전문학사과정",
        "D-2-2     학사유학    학사과정",
        "D-2-3     석사유학    석사과정",
        "D-2-4     박사유학    박사과정",
        "D-2-5     연구유학    학술연구기관특정연구자",
        "D-2-6     교환학생    교환학생",
        "D-2-F     교환학생    (구)교환학생",
        "D-2-S     시간취업    시간제아르바이트",
        "D-3-1     해투기술연수    해외투자,기술수출,산업설비수출",
        "D-3-B     중기협    수자",
        "D-3-C     수산협    수산업협동중앙회장연수자",
        "D-3-D     각부처    주무부처의장추천연수자",
        "D-3-E     건설협    건설협회추천",
        "D-3-F     농협    농업중앙회추천",
        "D-3-11    해외직접    해외직접투자",
        "D-3-12    기술수출    기술수출",
        "D-3-13    플랜트수출    플랜트수출",
        "D-4       일반연수    일반연수미상(미분류)",
        "D-4-1     대학부설어학원연수    대학부설어학원연수",
        "D-4-2     기타기관연수    대학부설어학원제외기타기관",
        "D-4-3     초중고생    고등학교이하재학생",
        "D-4-4     동포연수    무연고동포연수자",
        "D-4-5     한식조리연수    한식조리연수생",
        "D-4-6     사설기관연수    우수사설기관의외국인연수",
        "D-4-7     외국어연수    외국어연수생",
        "D-5       취재    취재",
        "D-6       종교    종교",
        "D-7       상사주재    상사주재",
        "D-7-1     외국기업    외국기업국내지사등에서주재",
        "D-7-2     내국기업    해외진출기업근무외국인력국내본사주재",
        "D-7-91    FTA전근    FTA기업내전근자",
        "D-7-92    FTA계약    FTA계약서비스공급자",
        "D-8       기업투자    기업투자",
        "D-8-1     법인에투자    법인에투자",
        "D-8-2     벤처투자    벤처투자",
        "D-8-3     개인기업투자    개인기업에투자",
        "D-8-4     기술창업    기술창업",
        "D-8-91    FTA전근    FTA기업내전근자",
        "D-9       무역경영    무역경영",
        "D-9-1     무역고유거래    무역업고유번호를부여받은무역거래자",
        "D-9-2     수출설비    수출설비기계의설치운영보수자",
        "D-9-3     선박설비    선박건조설비제작감독",
        "D-9-4     경영영리사업    회사경영및영리사업자",
        "E-1       교수    교수",
        "E-10-1    내항선원    내항선원",
        "E-10-2    어선원    어선원",
        "E-10-3    순항선원    순항여객선(크루즈)취업선원",
        "E-2-1     일반회화강사    일반회화지도강사",
        "E-2-2     학교보조교사    정부초청초중고교영어보조교사",
        "E-2-91    FTA영어    FTA영어보조교사",
        "E-3       연구    연구",
        "E-4       기술지도    기술지도",
        "E-5       전문직업    전문직업",
        "E-6       예술흥행    예술흥행미상(미분류)",
        "E-6-1     예술연예    음악,미술,문학등의예술,방송연예활동 ",
        "E-6-2     호텔유흥    호텔업시설,유흥업소등에서의공연 ",
        "E-6-3     운동    운동선수,프로팀감독,매니저",
        "E-7-1     특정활동    특정활동",
        "E-7-2     의료코디    의료코디네이터",
        "E-7-3     해삼양식    해삼양식기술자",
        "E-7-91    FTA독립    FTA독립전문가",
        "E-9-1     제조업    제조업",
        "E-9-2     건설업    건설업",
        "E-9-3     농업    비전문취업농업종사자",
        "E-9-4     어업    어업",
        "E-9-5     서비스업    냉장,냉동창고업 ",
        "E-9-6     재료수집    재생용재료수집업",
        "E-9-7     관광호텔    관광호텔업",
        "E-9-8     축산업    비전문취업축산업종사자",
        "E-9-A     음식업    외국국적동포일반,기타음식업종 ",
        "E-9-B     청소업    외국국적동포청소관련서비스업",
        "E-9-C     간병가사    외국국적동포개인간병가사서비스업",
        "E-9-D     건설업    외국국적동포건설업",
        "E-9-E     자차수리    외국국적동포자동차수리업",
        "E-9-F     제조업    외국국적동포제조업",
        "E-9-G     농축산업    외국국적동포농축산업",
        "E-9-H     연근해업    외국국적동포연근해어업",
        "E-9-I     욕탕업    외국국적동포욕탕업",
        "E-9-J     재료수집    외국국적동포재생용재료수집및판매업",
        "E-9-K     냉장냉동    외국국적동포냉장,냉동창고업 ",
        "E-9-L     비전문취업    비전문취업(합법화조치등)",
        "E-9-95    과거추천연수    과거단체추천형산업연수생",
        "E-9-96    과거연수취업    과거연수취업자격소지자",
        "E-9-97    과거특례고용    과거외국적동포비전문취업특례고용",
        "E-9-98    과거합법조치    과거합법조치후불법체류자",
        "F-1       방문동거    방문동거미분류",
        "F-1-1     방문동거    친척방문,가족동거,피부양,가사정리등 ",
        "F-1-2     가사보조    주한외국공관원등의가사보조인",
        "F-1-3     외교동거    주한외국공관원드의비세대동거인",
        "F-1-5     결혼이민가족    결혼이민자부모및가족",
        "F-1-6     결혼가사정리    결혼이민자이혼후가서정리등",
        "F-1-7     국적신청    국적신청자",
        "F-1-8     합법출생자녀     합법체류자의국내출생자녀 ",
        "F-1-9     동포배우자등    F4의배우자및미성년자녀",
        "F-1-10    동포고령    중국및구소련동포고령자",
        "F-1-11    방문취업자녀    H2의19세미만자녀",
        "F-1-12    거주배우자    F-2의배우자 ",
        "F-1-13    유학생부모    미성년유학생부모",
        "F-1-14    입양외국인    입양된외국인",
        "F-1-21    외교가사보조    주한외국공관원의가사보조인",
        "F-1-22    고액가사보조    고액투자가의가사보조인",
        "F-1-23    첨단가사보조    첨단투자가의가사보조인",
        "F-1-24    전문가사보조    전문인력등의가사보조인",
        "F-1-71    국적신청가족    국적신청자(외국국적동포)의배우자,자녀 ",
        "F-1-72    영주신청가족    영주자격(F-5-7)을신청한자의배우자,자녀",
        "F-1-99    기타동거    기타동거",
        "F-2       거주    거주미분류",
        "F-2-1     국민배우자    국민의배우자",
        "F-2-2     국민자녀    국민의미성년자녀,국민과혼인관계에서출생한사람 ",
        "F-2-3     영주자가족    영주자격소지자의배우자및미성년자녀",
        "F-2-4     난민    난민인정을받은자",
        "F-2-5     고액투자    고액투자장기체류외국인(3년이상) ",
        "F-2-6     숙련기능    숙련생산기능외국인력",
        "F-2-7     점수우수인력    점수제에의한우수전문인력",
        "F-2-8     부동산투자    부동산투자이민자",
        "F-2-9     영주상실    영주자격상실자",
        "F-2-10    자녀양육    국민과의혼인관계에서출생한자를양육하고있는부또는모",
        "F-2-11    공무임용    공무원으로임용된자",
        "F-2-12    공익사업투자    공익사업투자이민자",
        "F-2-13    공익은퇴가족    공익사업투자이민자나은퇴이민자의배우자및미성년자녀",
        "F-2-14    은퇴이민투자    은퇴이민투자자",
        "F-2-71    점수가족    점수제해당자의배우자및미성년자녀",
        "F-2-81    부동가족    부동산투자이민자의배우자및미성년자녀",
        "F-2-99    기타장기    기타장기체류자",
        "F-3-1     동반    동반",
        "F-3-91    FTA동반    FTA동반",
        "F-4-1     기여동포    국익기여재외동포",
        "F-4-2     일반동포    일반재외동포",
        "F-4-11    재외동포본인    비고시본인",
        "F-4-12    재외동포직계가족    비고시부모",
        "F-4-13    DE계열6개월이상체류자    DE체류자 ",
        "F-4-14    대학졸업자    대학졸업자",
        "F-4-15    OECD영주자    OECD영주자",
        "F-4-16    법인대표등    법인대표등",
        "F-4-17    10만불기업가    10만불기업가",
        "F-4-18    다국적기업    다국적기업임원등",
        "F-4-19    동포단체대표    동포단체대표등",
        "F-4-20    공무원등    공무원등",
        "F-4-21    교원    교원",
        "F-4-22    개인사업가    개인사업가",
        "F-4-23    빈번출입자    빈번출입자",
        "F-4-24    제조등근속자    제조업등근속자",
        "F-4-25    60세이상자    60세이상자",
        "F-4-26    수교전입국자    수교전입국자",
        "F-4-27    자격증소지자    자격증소지자",
        "F-4-99    재외동포기타    재외동포기타",
        "F-5       영주    영주미분류",
        "F-5-1     장기체류    5년이상대한민국에체류하고있는자 ",
        "F-5-2     국민배우자    국민의배우자",
        "F-5-3     국민자녀    국민의미성년자녀",
        "F-5-4     영주가족    영주자격소지자의배우자및미성년자녀",
        "F-5-5     고액투자    고액투자자로서5인이상국민고용자 ",
        "F-5-6     재외동포2년    재외동포자격으로2년이상체류자",
        "F-5-7     동포국적요건    외국국적동포로서국적취득요건을갖춘자",
        "F-5-8     재한화교    대한민국출생재한화교",
        "F-5-9     첨단박사    첨단산업분야박사학위소지자",
        "F-5-10    첨단학사    첨단산업분야학사학위또는자격증소지자",
        "F-5-11    특정능력    특정분야능력소유자",
        "F-5-12    특별공로    특별공로자",
        "F-5-13    연금수혜    연금수혜자",
        "F-5-14    방문취업4년    방문취업자격으로4년이상제조업종등근무자",
        "F-5-15    국내박사    국내대학원에서정규과정을마치고박사학위를취득한자",
        "F-5-16    점수제    점수제로거주자격취득후3년이상체류자 ",
        "F-5-17    부동산투자    부동산투자자와그배우자및미성년자녀",
        "F-5-18    점수가족    점수제로영주자격취득자의배우자및미성년자녀",
        "F-5-19    부동가족    부동산투자자로영주자격취득자의배우자및미성년자녀",
        "F-5-20    영주출생    영주자격자의국내출생자녀",
        "F-5-21    공익사업투자    공익사업투자자로5년이상계속투자상태를유지한상태 ",
        "F-5-22    공익은퇴가족    공익사업투자자로영주자격취득자의배우자및미성년자녀",
        "F-5-23    은퇴이민투자    은퇴이민투자자로투자상태유지등요건을갖춘사람",
        "F-5-24    법인창업영주    법인창업자격으로3년이상체류자 ",
        "F-5-25    고액투자조건부영주    15억원이상을투자하고5년간투자유지서약자 ",
        "F-6-1     국민배우자    국민의배우자",
        "F-6-2     자녀양육    자녀양육",
        "F-6-3     혼인단절    혼인단절",
        "G-1       기타    기타",
        "G-1-1     산재보상    산재보상청구및치료중인자와보호자",
        "G-1-2     질병사고    질병,사고로치료중인자와보호자 ",
        "G-1-3     소송진행    각종소송진행중인자",
        "G-1-4     체임중재    체불임금노동관서중재중인자",
        "G-1-5     난민신청    난민신청자",
        "G-1-6     난민인허    난민불인정자중인도적체류허가자",
        "G-1-7     가족사망    사고등으로사망한자의가족",
        "G-1-9     임신출산    임신,출산등인도적체류허가자 ",
        "G-1-M     의료관광    치료,요양하고자하는자및동반가족 ",
        "G-1-10    치료요양    외국인환자",
        "G-1-11    성매매피해자    성매매피해외국인여성등인도적고려가필요한자",
        "G-1-99    기타    기타",
        "H-1       관광취업    관광취업",
        "H-2-1     연고방취    연고자또는국가유공자",
        "H-2-2     유학방취    유학생의부모또는배우자",
        "H-2-3     자진방취    2006년자진출국자",
        "H-2-4     연수방취    연수취업후자진귀국자",
        "H-2-5     추첨방취    한국말시험등으로선발된자",
        "H-2-6     변경방취    무연고동포연수후자격변경자",
        "H-2-7     만기방취    만기출국자재입국자",
        "H-2-99    기타방취    기타(자격변경자포함)",
        "T-1-1     관광상륙    선박도착관광상륙허가자"
      ],
      countrys: [
        "5   아프가니스탄",
        "9   알바니아",
        "11   남극",
        "13   알제리",
        "17   아메리칸사모아",
        "21   안도라",
        "25   앙골라",
        "29   앤티가 바부다",
        "32   아제르바이잔",
        "33   아르헨티나",
        "37   오스트레일리아",
        "41   오스트리아",
        "45   바하마",
        "49   바레인",
        "51   방글라데시",
        "52   아르메니아",
        "53   바베이도스",
        "57   벨기에",
        "61   버뮤다",
        "65   부탄",
        "69   볼리비아",
        "71   보스니아 헤르체고비나",
        "73   보츠와나",
        "75   부베 섬",
        "77   브라질",
        "85   벨리즈",
        "87   영국령 인도양 지역",
        "91   솔로몬 제도",
        "93   영국령 버진아일랜드",
        "97   브루나이",
        "101   불가리아",
        "105   미얀마",
        "109   부룬디",
        "113   벨라루스",
        "117   캄보디아",
        "121   카메룬",
        "125   캐나다",
        "133   카보베르데",
        "137   케이맨 제도",
        "141   중앙아프리카 공화국",
        "145   스리랑카",
        "149   차드",
        "153   칠레",
        "157   중화인민공화국",
        "159   중화민국",
        "163   크리스마스 섬",
        "167   코코스 제도",
        "171   콜롬비아",
        "175   코모로",
        "176   마요트",
        "179   콩고 공화국",
        "181   콩고 민주 공화국",
        "185   쿡 제도",
        "189   코스타리카",
        "192   크로아티아",
        "193   쿠바",
        "197   키프로스",
        "204   체코",
        "205   베냉",
        "209   덴마크",
        "213   도미니카 연방",
        "215   도미니카 공화국",
        "219   에콰도르",
        "223   엘살바도르",
        "227   적도 기니",
        "232   에티오피아",
        "233   에리트레아",
        "234   에스토니아",
        "235   페로 제도",
        "239   포클랜드 제도",
        "240   사우스조지아 사우스샌드위치 제도",
        "243   피지",
        "247   핀란드",
        "249   올란드 제도",
        "251   프랑스",
        "255   프랑스령 기아나",
        "259   프랑스령 폴리네시아",
        "261   프랑스령 남부와 남극 지역",
        "263   지부티",
        "267   가봉",
        "269   조지아",
        "271   감비아",
        "276   팔레스타인",
        "277   독일",
        "289   가나",
        "293   지브롤터",
        "297   키리바시",
        "301   그리스",
        "305   그린란드",
        "309   그레나다",
        "313   과들루프",
        "317   괌",
        "321   과테말라",
        "325   기니",
        "329   가이아나",
        "333   아이티",
        "335   허드 맥도널드 제도",
        "337   바티칸 시국",
        "341   온두라스",
        "345   홍콩",
        "349   헝가리",
        "353   아이슬란드",
        "357   인도",
        "361   인도네시아",
        "365   이란",
        "369   이라크",
        "373   아일랜드",
        "377   이스라엘",
        "381   이탈리아",
        "385   코트디부아르",
        "389   자메이카",
        "393   일본",
        "399   카자흐스탄",
        "401   요르단",
        "405   케냐",
        "409   조선민주주의인민공화국",
        "411   대한민국",
        "415   쿠웨이트",
        "418   키르기스스탄",
        "419   라오스",
        "423   레바논",
        "427   레소토",
        "429   라트비아",
        "431   라이베리아",
        "435   리비아",
        "439   리히텐슈타인",
        "441   리투아니아",
        "443   룩셈부르크",
        "447   마카오",
        "451   마다가스카르",
        "455   말라위",
        "459   말레이시아",
        "463   몰디브",
        "467   말리",
        "471   몰타",
        "475   마르티니크",
        "479   모리타니",
        "481   모리셔스",
        "485   멕시코",
        "493   모나코",
        "497   몽골",
        "499   몰도바",
        "500   몬테네그로",
        "501   몬트세랫",
        "505   모로코",
        "509   모잠비크",
        "513   오만",
        "517   나미비아",
        "521   나우루",
        "525   네팔",
        "529   네덜란드",
        "531   네덜란드령 안틸레스",
        "534   아루바",
        "541   누벨칼레도니",
        "549   바누아투",
        "555   뉴질랜드",
        "559   니카라과",
        "563   니제르",
        "567   나이지리아",
        "571   니우에",
        "575   노퍽 섬",
        "579   노르웨이",
        "581   북마리아나 제도",
        "582   미국령 군소 제도",
        "584   미크로네시아 연방",
        "585   마셜 제도",
        "586   팔라우",
        "587   파키스탄",
        "592   파나마",
        "599   파푸아 뉴기니",
        "601   파라과이",
        "605   페루",
        "609   필리핀",
        "613   핏케언 제도",
        "617   폴란드",
        "621   포르투갈",
        "625   기니비사우",
        "627   동티모르",
        "631   푸에르토리코",
        "635   카타르",
        "639   레위니옹",
        "643   루마니아",
        "644   러시아",
        "647   르완다",
        "655   세인트헬레나",
        "660   세인트키츠 네비스",
        "661   앵귈라",
        "663   세인트루시아",
        "667   생피에르 미클롱",
        "671   세인트빈센트 그레나딘",
        "675   산마리노",
        "679   상투메 프린시페",
        "683   사우디아라비아",
        "687   세네갈",
        "689   세르비아",
        "691   세이셸",
        "695   시에라리온",
        "703   싱가포르",
        "704   슬로바키아",
        "705   베트남",
        "706   슬로베니아",
        "707   소말리아",
        "711   남아프리카 공화국",
        "717   짐바브웨",
        "725   스페인",
        "729   남수단",
        "733   서사하라",
        "737   수단",
        "741   수리남",
        "745   스발바르 얀마옌",
        "749   스와질란드",
        "753   스웨덴",
        "757   스위스",
        "761   시리아",
        "763   타지키스탄",
        "765   타이",
        "769   토고",
        "773   토켈라우",
        "777   통가",
        "781   트리니다드 토바고",
        "785   아랍에미리트",
        "789   튀니지",
        "793   터키",
        "796   투르크메니스탄",
        "797   터크스 케이커스 제도",
        "799   투발루",
        "801   우간다",
        "805   우크라이나",
        "808   마케도니아 공화국",
        "819   이집트",
        "827   영국",
        "832   건지 섬",
        "833   저지 섬",
        "834   맨 섬",
        "835   탄자니아",
        "841   미국",
        "851   미국령 버진아일랜드",
        "855   부르키나파소",
        "859   우루과이",
        "861   우즈베키스탄",
        "863   베네수엘라",
        "877   왈리스 퓌튀나",
        "883   사모아",
        "888   예멘",
        "895   잠비아"
      ]
    }
  },
  getters: {
    receiptLengths: state => {
      var objReceipt = Object.values(state.all.receipts);
      var lengths = {};
      lengths.apply = objReceipt.filter(receipt => receipt.meta.state == 'apply' ? true : false).length;
      lengths.refuse = objReceipt.filter(receipt => receipt.meta.state == 'refuse' ? true : false).length;
      lengths.cancel = objReceipt.filter(receipt => receipt.meta.state == 'cancel' ? true : false).length;
      lengths.accept = objReceipt.filter(receipt => receipt.meta.state == 'accept' ? true : false).length;
      lengths.sum = +lengths.apply + +lengths.refuse + +lengths.cancel + +lengths.accept;
      return lengths;
    },
    user: state => {
      return state.all.users[state.all.id];
    },
    snackbar: state => {
      var r = {
        primary: { color: 'blue', icon: 'done', },
        success: { color: 'green', icon: 'done', },
        warning: { color: 'orange', icon: 'warning' },
        error: { color: 'red lighten-2', icon: 'error' },
        info: { color: 'teal', icon: 'info' }
      }[state.snackbar.type]
      return r;
    },
    isAdmin: state => {
      return state.all.id == 'admin';
    },
    receipt: state => {
      if (state.receipt.receipts.hasOwnProperty(state.receipt.selected))
        return state.receipt.receipts[state.receipt.selected];
      else
        return {};
    }

  },
  mutations: {
    updateWindowSize(state) {
      state.windowSize = { x: window.innerWidth, y: window.innerHeight };

    },
    chat(state, payload) {
      state.chat.state = !state.chat.state;
      state.chat.room = payload;
    },
    /**
     * success
     * type ( 업데이트할 변수 )
     * name ( 업데이트 종류 )
     */
    snackbar(state, payload) {
      if (payload.pre) {
        switch (payload.pre) {
          case 'err_form':
            state.snackbar.text = '채워지지 않았거나 오류가 있는 입력란이 있는지 확인해주세요.';
            state.snackbar.type = 'error';
            break;
        }
      }
      else {
        state.snackbar.text = payload.text;
        state.snackbar.type = payload.type;
      }
      state.snackbar.on = true;
      // state.snackbar.icon = payload.icon;
    },
    /**
     * success
     * type
     * name
     */
    update: (state, payload) => {
      let data = {};
      let success_self = () => { };
      let ajaxurl = state.ajaxurl;
      let success = (res) => { };
      let error = (res) => { };
      if (payload.success) success = payload.success;
      if (payload.error) error = payload.error;

      switch (payload.type) {
        case 'users':
          switch (payload.name) {
            case 'get':
              data = {
                type: 'user',
                name: 'get',
              }
              success_self = result => {
                var id = result['아이디'];
                state.all.users[id] = result;
                state.all.id = id;
              }
              break;
            case 'get_all':

              data = {
                type: 'user',
                name: 'get_all',
              }
              success_self = result => {
                state.all.users = result;
              }
              break;
          }
          break;
        case 'receipts':
          switch (payload.name) {
            case 'remove_all':
              state.all.receipts = {};
              break;
            case 'get':
              data = {
                type: 'receipt',
                name: 'get',
                bag: {
                  indexs: [payload.bag.index]
                }
              }

              success_self = result => {
                state.all.receipts[payload.bag.index] = result[payload.bag.index];
              }
              break;
            case 'get_all':
              data = {
                type: 'receipt',
                name: 'get_all',
              }
              success_self = result => {
                state.all.receipts = result;
              }
              break;
            case 'get_users':
              data = {
                type: 'receipt',
                name: 'get_user',
              }
              success_self = result => {
                state.all.receipts = result;
              }
              break;
          }
          break;
        case 'settings':
          data = {
            type: 'setting',
            name: 'get',
          }
          success_self = result => {
            state.all.settings = result;
          }
          break;
        case 'formats':
          data = {
            type: 'format',
            name: 'get',
          }
          success_self = result => {
            state.all.formats = result;
          }
          break;
        case 'chats':
          data = {
            type: 'chats',
            name: 'get',
            bag: {
              room: payload.bag.room
            }
          }
          success_self = result => {
            state.all.chats[payload.bag.room] = result;
          }
          break;
      }
      if (Object.keys(data).length) {
        $.post(ajaxurl, data, (res) => {
          try {
            res = JSON.parse(res);
          } catch (e) {
            console.warn("RES: ", res);
            console.warn("DATA: ", data);
            console.warn("ERROR", e);

            return;
          }
          if (res.success) {
            console.log(res)
            success_self(res.result);
            success(res.result);
          }
          else {
            error(res.result);
            console.warn("PAYLOAD", payload);
            console.warn("Res", res);
          }
        })
      }
    }

  },
  actions: {
    FileDownload(_, filePath) {
      var a = document.createElement("A");
      a.href = filePath;
      a.download = filePath.substr(filePath.lastIndexOf("/") + 1);
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    },
    /**
     * 
     * data,
     * success,
     * error
     */
    post(store, payload) {
      var data = payload.data;
      var success = () => { }
      var error = () => { }
      if (payload.success) success = payload.success;
      if (payload.error) error = payload.error;
      $.post(store.state.ajaxurl, data, res => {
        try {
          res = JSON.parse(res);
        } catch (e) {
          console.warn("RES: ", res);
          console.warn("DATA: ", data);
          console.warn("E: ", e);

          return;
        }
        if (!res.success) {
          console.warn(res);
          error(res.result);
          return;
        }
        success(res.result);
      })
    }
  },

})
