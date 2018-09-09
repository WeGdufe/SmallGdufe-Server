# 小广财API接入指南

[TOC]  

[API文档列表戳这](http://www.wegdufe.com/apidoc)

小广财API文档自 v1.1.0 开始进行对学号密码的加密，为了保证安全，需要接入方 使用一些通用参数来代替学号密码明文。

在接入前你需要联系管理员 ( @wintercoder QQ:`792875586` ) 获取  `appid` 和 `secretKey` 

另外如果接入不畅，可以使用  [接入参数生成接口 
](http://www.wegdufe.com/apidoc/#api-Work-gen_test_url)
进行参数对比

## 通用参数

### appid
管理员给你的，标识接入来源的字符串。  
同时管理还会给你个 secretKey，用于 `token` 参数的计算，请保存好 `secretKey` ，该 key 泄露可能会导致 Hacker 能解析你的请求参数，进而拿到明文密码


### timestamp
当前时间戳，秒级别，不解释

### token

字段目的是：对学号、密码参数进行加密。  
因为小广财本身不存密码，所以需要能反解的加密方案，如下：

1. 拼接字符串：组成 `sno=学号&pwd=密码`  字符串
2. AES加密： 对 `sno=学号&pwd=密码` 进行 AES加密 (AES-128-CBC/PKCS7Padding填充，加密完附带 base64），KEY 为 `secretkey+时间戳` （没有加号，样例：hello1536490559） 
3. 进行urlencode： 对token进行urlencode：避免 加号、等号 被浏览器转码

**PHP样例**

```
$secretKey = '哈哈'; $timestamp = time(); 
$str = "sno=13251102210&pwd=helloworld";

$token = AesSecurity::encrypt($str,$secretKey. $timestamp);
$token = urlencode($token);	//你要的结果
        

class AesSecurity
{
    /**
     * @param $input string 原数据
     * @param $key string key
     * @return string 加密密文
     */
    public static function encrypt($input, $key)
    {
        $data  =  openssl_encrypt($input, 'AES-128-CBC',$key,0,$key);
//        $data = base64_encode($data);
        return $data;
    }

    /**
     * @param $input string 加密密文
     * @param $key string key
     * @return string 解密
     */
    public static function decrypt($input, $key)
    {
        $decrypted = openssl_decrypt($input, 'AES-128-CBC', $key, 0, $key);
        return $decrypted;
    }
}
```
**Android/JAVA样例**  
代码是安卓的，纯JAVA的话部分函数需要替换

```
long timestamp = System.currentTimeMillis() / 1000;
String privateContent =  "sno=" + AppConfig.sno + "&pwd=" +  password;
String secret = '哈哈';
String key = secret + String.valueOf(timestamp);

String token = ApiAESUtils.encrypt(privateContent, key );
token = URLEncoder.encode(token);
```
```

import android.util.Base64;

import com.apkfuns.logutils.LogUtils;

import javax.crypto.Cipher;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;

public class ApiAESUtils
{

    //此函数是pkcs7padding填充函数
    private static String pkcs7padding(String data) {
        int bs = 16;
        int padding = bs - (data.length() % bs);
        String padding_text = "";
        for (int i = 0; i < padding; i++) {
            padding_text += (char)padding;
        }
        return data+padding_text;
    }
    /**
     * AES加密
     * @param content 要加密的内容
     * @param key 密钥
     * @param iv iv
     * @return
     */
    public static String encrypt(String content, String key, String iv)
    {
        if(key == null || key.length() != 16)
        {
            System.err.println("AES key 的长度必须是16位！");
            return null;
        }
        if(iv == null || iv.length() != 16)
        {
            System.err.println("AES iv 的长度必须是16位！");
            return null;
        }
        try
        {
            content = pkcs7padding(content);        //JAVA不支持，所以手动进行PKCS7Padding填充
            Cipher cipher = Cipher.getInstance("AES/CBC/NoPadding");
            int blockSize = cipher.getBlockSize();
            byte[] dataBytes = content.getBytes();
            int plaintextLength = dataBytes.length;
            if (plaintextLength % blockSize != 0)
            {
                plaintextLength = plaintextLength + (blockSize - (plaintextLength % blockSize));
            }
            byte[] plaintext = new byte[plaintextLength];
            System.arraycopy(dataBytes, 0, plaintext, 0, dataBytes.length);
            SecretKeySpec keyspec = new SecretKeySpec(key.getBytes(), "AES");
            IvParameterSpec ivspec = new IvParameterSpec(iv.getBytes());
            cipher.init(Cipher.ENCRYPT_MODE, keyspec, ivspec);
            byte[] encrypted = cipher.doFinal(plaintext);

            String base64ed = Base64.encodeToString(encrypted, Base64.DEFAULT);
            //                    new BASE64Encoder().encode(encrypted);

            //坑爹base64超过一定长度会换行，会导致各种报错
            base64ed = base64ed.replaceAll("\r\n", "").replaceAll("\r", "")
                    .replaceAll("\n", "");
            return base64ed;
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return null;
    }

    /**
     * AES加密，key和iv一样
     * @param content 要加密的内容
     * @param key 密钥
     * @return
     */
    public static String encrypt(String content, String key)
    {
        return encrypt(content, key, key);
    }    
}
```

### sign
字段为参数签名，避免抓包修改参数，计算方法：
    
除 路由的r 和 sign 参数本身 以外的全部参数，组成 `appid=哈哈&parms1=哈哈 ` 格式，对KEY进行字典序排序，然后 MD5


***PHP样例***

```
		//$req 为输入的各种参数，包含接口参数和自定义的那些app版本之类的
		$req = [
            'r'=> 'work/gen-test-url',
            'token'=> '我是token',
            'timestamp' => time(),
            '我是参数'=> '我是值',
        ];
        unset($req['r'],$req['sign']);
        ksort($req);	//key排序
        $backParamsUrl = http_build_query($req);	//组成url格式
        $backParamsUrl = rawurldecode($backParamsUrl);    //http_build_query后需要decode
        $sign = md5($backParamsUrl);

```
**JAVA样例**  

因为目前使用的是安卓特有的框架，所以此处暂无好的样例，到时谁接入了，帮更新下？

```
StringBuilder buffer = new StringBuilder();
for (int i = 0; i < nameList.size(); i++) {
    if ( nameList.get(i).equals("r") ){
        continue;
    }
    if(i != 0){
        buffer.append("&");
    }
    buffer.append(nameList.get(i)).append("=").append(httpUrl.queryParameterValues(nameList.get(i)) != null &&
            httpUrl.queryParameterValues(nameList.get(i)).size() > 0 ? httpUrl.queryParameterValues(nameList.get(i)).get(0) : "");
}
String sign = MD5Util.MD5(buffer.toString(buffer);
```