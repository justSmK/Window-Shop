#!/usr/bin/env python3
import mysql.connector

def write_image(conn, id, name, path):
    with open(path, "rb") as file:
        data = file.read()
        cursor = conn.cursor()
        cursor.execute("INSERT INTO `image`(id, name, data) VALUES (%s, %s, %s)", (id, name, data))
        conn.commit()

cnx = mysql.connector.connect(user='root', password='semko777',
                              host='localhost',
                              database='ShopDB',
                              auth_plugin='mysql_native_password')

for i in range(0, 14):
    write_image(cnx, i + 1, f"{i + 1}.png", f"site/img/{i + 1}.png")

write_image(cnx, 15, "author.png", "site/img/author.png")
write_image(cnx, 16, "header.png", "site/img/header.png")

cnx.close()
