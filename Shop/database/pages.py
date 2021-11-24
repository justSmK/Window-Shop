#!/usr/bin/env python3
import mysql.connector

def write_page(conn, page_path, path, mime_type):
    with open(path, "r") as file:
        data = file.read()
        cursor = conn.cursor()
        cursor.execute("INSERT INTO `page`(path, data, mime_type) VALUES (%s, %s, %s)", (page_path, data, mime_type))
        conn.commit()

conn = mysql.connector.connect(user='root', password='semko777',
                              host='localhost',
                              database='ShopDB',
                              auth_plugin='mysql_native_password')

write_page(conn, "/Shop/", "site/index.html", "text/html")
write_page(conn, "/Shop/style.css", "site/style.css", "text/css")
write_page(conn, "/Shop/about", "site/about.html", "text/html")

conn.close()