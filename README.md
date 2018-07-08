# 小广财服务端-安装指南


requirements: PHP >= 5.5.9 建议直接上php7  Redis >= 3.x  Mysql/MariaDB

clone完代码建议直接全部文件777权限 `chmod -R 777 . `

## 从0开始搭建

Redis:

    yum install -y redis 
    nohup redis-server  &

Nginx: 

    yum install  -y nginx 
    systemctl start nginx
    systemctl enable nginx

Mysql(mariadb):

    yum install mariadb-server mariadb -y
    初始化密码 mysql_secure_installation
    systemctl start mariadb
    systemctl enable mariadb

PHP7:

    yum -y install epel-release
    wget http://rpms.remirepo.net/enterprise/remi-release-7.rpm
    rpm -Uvh remi-release-7.rpm

    yum install yum-utils -y
    yum-config-manager --enable remi-php71
    yum --enablerepo=remi,remi-php71 install -y php-fpm php-common
    yum --enablerepo=remi,remi-php71 install -y  php-opcache php-pecl-apcu php-cli php-pear php-pdo php-mysqlnd php-pgsql php-pecl-mongodb php-pecl-redis php-pecl-memcache php-pecl-memcached php-gd php-mbstring php-mcrypt php-xml php-zip 

    systemctl start  php-fpm
    systemctl enable  php-fpm
    
    如遇问题可参考： https://www.hostinger.com/tutorials/how-to-install-lemp-centos7
    

新建数据库用户，避免ROOT账号连接

    mysql -u root -p
    CREATE USER 'gdufeuser'@'localhost' IDENTIFIED BY '密码';
    GRANT SELECT, INSERT, UPDATE, DELETE, CREATE ON *.* TO 'gdufeuser'@'localhost';
    GRANT SELECT, INSERT, UPDATE, REFERENCES, DELETE, CREATE ON `gdufeapp`.* TO  'gdufeuser'@'localhost';
    flush privileges;
然后更改config/db.php配置


如果想远程root连接数据库

    mysql -u root -p 密码
    GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '你的密码' WITH GRANT OPTION;
    flush privileges;


## 可选(使用PHP5)
如果你目前是PHP5且不想用PHP7，如果是5.3版本请升级到至少5.5.9，所以直接5.6或者7.x就方便

Centos6: https://www.zerostopbits.com/how-to-install-upgrade-php-5-3-to-php-5-5-on-centos-6-7/

Centos7: https://www.cadch.com/modules/news/article.php?storyid=227

EPEL和remi要装对应版本，Centos7就装7的，如果报了 `Requires: httpd-mmn = 20051115` 类似错误说明是你Centos7装了remi6，你需要 `remove` 掉6的再装7的。


## 导入初始数据库

新建数据库，名字叫`gdufeapp`，导入[gdufeapp.sql](./install_guide/gdufeapp.sql)，修改 `config/db.php` 的数据库密码等配置，在不用客户端的情况下命令如下

```
mysql -uroot -p
MySQL [(none)]> create database gdufeapp;
MySQL [(none)]> use gdufeapp;
MySQL [gdufeapp]> source 绝对路径/gdufeapp.sql;
MySQL [gdufeapp]> exit;
```

## 解决代码库依赖
代码需要一堆第三方库，在`vendor`目录里，但因`vendor`目录在`.gitignore`里，所以在Github上下载不到，方案有两个。
 - 方案一：在[Github的Release页面下载](https://github.com/WeGdufe/SmallGdufe-Server/releases) 或者 找现有服务器下载copy过来，`unzip vendor.zip` 解压放到项目根目录，这样就不用安装composer了

 - 方案二：安装`composer`，然后跑命令在线下载，这种方案少了/vender/bower/目录，不过那个是错误页，少了没关系

    ```
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    composer config -g repo.packagist composer https://packagist.phpcomposer.com # 设置镜像加速用
    yum -y install php-mbstring php-xml # 防依赖库报错用
    composer install 					# 让composer去下载依赖库
    ```


## 运行
### Nginx配置
1. 复制并编辑[nginx样例配置](./install_guide/gdufe.conf)替换 `/etc/nginx/nginx.conf` 里的Server部分

2. 由于样例是Nginx通过sock文件跟PHP交互，所以
`vi /etc/php-fpm.d/www.conf`
把 `listen = 127.0.0.1:9000` 改为 `listen = /var/run/php-fpm/php-fpm.sock`
  
3.  然后重启Nginx和php-fpm
     `systemctl restart  php-fpm`  `systemctl restart nginx`，尝试访问你的iP:82端口，如果出现Json字符串说明成功
   如果你全程是root账号操作你可能需要 尝试 
     `chmod o+w /var/run/php-fpm/php-fpm.sock`
   启动失败请看日志：
      `/var/log/nginx/error.log`
      `/usr/share/nginx/MovingGdufe-Server/runtime/logs`

### Yii自带Web服务器，在项目目录下

    php yii serve app.wintercoder.com:82
就行。但这个是单线程的，只能测试用，不要线上用，否则更新APP的时候可能卡全部用户。


### Httpd 配置

直接监听82端口，目录指到项目根目录的`/web目录`就行，如果代码放在默认的`/var/www/html/`目录下，直接如下操作就行。

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
2. 访问 `http://你的ip:82/index.php?r=work/feedback&sno=13251102210&content=test` 能通说明数据库OK
3. 能查成绩且第二次打开会快很多则说明redis没问题 `http://你的ip:82/index.php?r=jw/get-grade&sno=你的学号&pwd=你的密码`

## 更新Api文档
Api文档是用Apidoc生成的，需要NodeJs环境，以下是按Win来说明的，Linux和Mac操作同理
1. 安装NodeJs： http://www.runoob.com/nodejs/nodejs-install-setup.html
   装完后安装apidoc: sudo npm install apidoc -g

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

- 修改Mysql的Root密码，连上mysql再
  ```
  SET PASSWORD FOR 'root'@'localhost' = PASSWORD('新密码');
  FLUSH PRIVILEGES;
  ```
