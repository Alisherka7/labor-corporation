import os

names = [name for _,_, name in os.walk("./")][0]
    

names_png = [name.replace("PNG", "png") for name in names]

os.makedirs("to", exist_ok=True)
for i, v in enumerate(names):
    with open(v, "rb") as fp:
        b = fp.read()
    with open("to/" + names_png[i], "wb") as fp:
        fp.write(b)
