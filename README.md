# 移动广财服务端-安装指南

**非开源** 

requirements: PHP >= 5.5.9  Redis >= 3.x  Mysql/MariaDB

clone完代码建议直接全部文件777权限 `chmod -R 777 . `

提供了个 [insatall.sh安装脚本](./insatall.sh)  来安装redis和compsoer，使用后无错就配置数据库和Httpd就行，`chmod +x install.sh & bash ./insatall.sh `。

不过还是推荐手动复制下面那些命令去操作。

## 更新PHP版本
如果你PHP是5.3的请升级到至少5.5.9，所以直接5.6或者7.x就方便

Centos6: https://www.zerostopbits.com/how-to-install-upgrade-php-5-3-to-php-5-5-on-centos-6-7/

Centos7: https://www.cadch.com/modules/news/article.php?storyid=227

EPEL和remi要装对应版本，Centos7就装7的，如果报了 ` Requires: httpd-mmn = 20051115` 类似错误说明是你Centos7装了remi6，你需要 `remove` 掉6的再装7的。

## 安装redis
- 安装可用yum安装 或者源码安装。推荐yum安装，跑如下命令就行

```
yum -y install epel-release 				 # 上面如果升级PHP版本时，就不用安装这个了
yum -y install redis
redis-server /usr/local/redis/etc/redis.conf # 运行
ps aux | grep redis  						 # 这里应该能看到redis在运行了
```

- 或者源码安装redis https://redis.io/download ，建议3.x

1. 关闭保护模式 protected mode  
   修改redis源码安装目录下的redis.conf，如下修改参数

   		daemonize yes
   		protected-mode no


2. 运行redis，在redis目录下 ： 

  ```
  redis-server redis.conf
  ```
## 配置数据库

安装数据库就不说了，不会的[看这里](https://support.rackspace.com/how-to/installing-mysql-server-on-centos/) 。

装完新建数据库，名字叫`gdufeapp`，导入[gdufeapp.sql](./gdufeapp.sql)，修改 `config/db.php` 的数据库密码等配置，在不用客户端的情况下命令如下

```
mysql -uroot -p
MySQL [(none)]> create database gdufeapp;
MySQL [(none)]> use gdufeapp;
MySQL [gdufeapp]> source 绝对路径/gdufeapp.sql;
MySQL [gdufeapp]> exit;
vi config/db.php
```

## 解决代码库依赖
代码需要一堆第三方库，在`vendor`目录里，但因`vendor`目录在`.gitignore`里，所以在Github上下载不到，方案有两个。
 - 方案一：找现有服务器下载copy过来，放到项目根目录，这样就不用安装composer了，直接去运行把。

 - 方案二：安装`composer`，然后跑命令在线下载就行，想看详情的去[官网。](http://docs.phpcomposer.com/00-intro.html#Installation-*nix)

    ```
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    composer config -g repo.packagist composer https://packagist.phpcomposer.com # 设置镜像加速用
    yum -y install php-mbstring php-xml # 防依赖库报错用
    composer install 					# 让composer去下载依赖库
    ```



## 运行
Yii自带Web服务器，在项目目录下  

    php yii serve app.wintercoder.com:82
就行。但这个是单线程的，只能测试用，不要线上用，否则更新APP的时候可能卡全部用户。

`Nginx` 配置较麻烦，推荐用 `Httpd`

### Httpd 配置

配置非常简单，直接监听82端口，目录指到项目根目录的`/web目录`就行，如果代码放在默认的`/var/www/html/`目录下，直接如下操作就行。

` vi /etc/httpd/conf/httpd.conf `

```
Listen 82
NameVirtualHost *:82
<VirtualHost *:82>
ServerName 这里是服务器IP/域名
DocumentRoot "/var/www/html/web"
</VirtualHost>
```

完工。如果不在`/var/www/html/`里，你跟着改路径就行。

如果同时跑Web官网和接口，添加如下：

```
NameVirtualHost *:82
<VirtualHost *:82>
ServerName app.wintercoder.com
DocumentRoot "服务端代码根目录地址/web"
</VirtualHost>

NameVirtualHost *:82
<VirtualHost *:82>
ServerName api.wintercoder.com
DocumentRoot "服务端代码根目录地址/web"
</VirtualHost>

NameVirtualHost *:8080
<VirtualHost *:8080>
ServerName www.wintercoder.com
DocumentRoot "官网代码根目录地址"
</VirtualHost>
然后DocumentRoot "这目录下包含服务端代码目录和官网目录" 
```

启动 `service httpd start`

### 检验成功与否

记得关闭`iptables`或者开放`82,8080`端口

1. 能浏览器打开（GET） `http://你的ip:82/index.php?r=work/check-app-update` 且返回json则说明环境通了
2. 能查成绩且第二次打开会快很多则说明redis没问题 `http://你的ip:82/index.php?r=jw/get-grade&sno=你的学号&pwd=你的密码`
3. 修改完APP的域名指向到你服务器，测试下反馈功能就行了

## 更新Api文档
Api文档是用Apidoc生成的，需要NodeJs环境，以下是按Win来说明的。
1. 安装NodeJs： http://www.runoob.com/nodejs/nodejs-install-setup.html

2. 解决国内npm命令安装依赖速度慢的问题：

   npm config set registry http://registry.npm.taobao.org

3. 修改完 `controller/` 的注释就在项目根目录就可以生成到 `apidoc` 目录下

   apidoc -i controllers/ -o apidoc/

## 安装中可能遇到的问题

- ` MISCONF Redis is configured to save RDB snapshots, but is currently not able to persist on disk. `

​     解决方案： 在redis-cli里运行 127.0.0.1:6379> ` config set stop-writes-on-bgsave-error no`

- HTTPD显示PHP文件为源码或者自动下载
  ` vi /etc/httpd/conf/httpd.conf `

   ```
  AddHandler  php5-script     PHP
  AddType application/x-httpd-php .php
  AddType application/x-httpd-php-source .phps
  <IfModule prefork.c>
  LoadModule php5_module modules/libphp5.so
  </IfModule>
  <IfModule !prefork.c>
  LoadModule php5_module modules/libphp5-zts.so
  </IfModule>
   ```


​    如果加载不到这几个.so的报错就可能PHP你不是通过yum安装的

- 该系统在Win上也可安装，最开始是在Win上测试的，不过安装软件就需要自己百度去官网下载了。