#!/bin/bash
apidoc -i controllers/ -o apidoc/
 cp -r apidoc/* ../SmallGdufe-Web/apidoc/
# C:\Users\xiaoguang\AppData\Roaming\npm\apidoc -i controllers/ -o apidoc/
# 注意不能命名为apidoc 避免无限递归
