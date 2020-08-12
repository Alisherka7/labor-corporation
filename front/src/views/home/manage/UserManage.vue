<template>
  <div>

    <divider1 title="회원 관리">
      <divider3 title="회원 관리 테이블" subtitle="회원 정보를 볼 수 있습니다.">
        <UserManageTable></UserManageTable>

      </divider3>
    </divider1>
  </div>
</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import UserManageTable from "@/components/vision/user/UserManageTable";
export default {
  name: "UserManage",
  mounted(){
    window.scrollTo(0, 0);

  },
  data() {
    return {};
  },
  components: {
    UserManageTable,
    Divider1,
    Divider3
  }
};
</script>
