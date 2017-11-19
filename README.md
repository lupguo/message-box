### gRPC+protobuf的调试
> protobuf： 谷歌出品的rpc序列化+反序列化的工具，能较高性能的数据交换，与thrift、avro这类属于同类型产品；
> 同时客户端和服务端都遵循IDL约定，同时需要proto编译工具的提前支持。

> gRPC: 谷歌出品的rpc解决方案，默认是依托于protobuf协议；

### 特性
1. 基于protobuf，针对消息进行了编码/解码；
2. 基于composer包方式管控，容易引入到项目中；
3. 目录结构清晰;

### 准备工作

#### 1、安装protoc编译器：https://github.com/google/protobuf

#### 2. RPC目录结构说明：
- 客户端相关；
- 异常管控；
- Protobuf相关(Idl、Request、Response响应等)；
- 模拟RPC服务调试；
- 传输方式基于(Stream|Socket)等；
	```
	src/
	└── Rpc
	    ├── Autoloader.php
	    ├── Client
	    │   └── RpcClient.php
	    ├── Exceptions		
	    │   ├── ClientException.php
	    │   ├── MessageException.php
	    │   ├── RpcException.php
	    │   └── TransportException.php
	    ├── Message
	    │   ├── InterfaceMessageBox.php
	    │   └── SoaMessageBox.php
	    ├── Protobuf
	    │   ├── GPBMetadata
	    │   │   ├── Request.php
	    │   │   └── Response.php
	    │   ├── Idl
	    │   │   ├── Request.proto
	    │   │   └── Response.proto
	    │   └── Message
	    │       └── Payload
	    │           ├── Request_Header.php
	    │           ├── Request.php
	    │           ├── Response_Header.php
	    │           └── Response.php
	    ├── Response
	    ├── Server
	    │   └── StreamServerDemon.php
	    └── Transport
	        ├── Http
	        │   └── HttpTransport.php
	        ├── InterfaceTransport.php
	        ├── Socket
	        └── Stream
	            ├── SoaStreamTransport.php
	            └── StreamTransport.php
	```
#### 3. 执行相关protoc文件的编译（由于只用到到`Request.proto`和`Response.proto`，做了预编译，此步骤可以忽略）：
```
$ cd src/Rpc/Protobuf/
$ protoc -I Idl/ --php_out=. Request.proto Response.proto
```
#### 4. 调试
> 模拟起一个RPC服务端，提供数据接收和响应

1. 启动`模拟的rpc server`: 
```
vagrant@homestead:~/www/open-platform/grpc-protobuf/public$ php server.php 
LISTEN ON : [ tcp://192.168.10.10:43217 ]
CLIENT FROM: [ 192.168.10.10:34924 ] 
CLIENT FROM: [ 192.168.10.10:34928 ] 
```
2. 配置域名、请求后解析得到
```
# rpc protobuf
192.168.10.10  rpc-protobuf.net 
```
3. web客户端环境配置好（基于vagrant的需要提前做好相关web的部署）
4. 请求输出
```
/home/vagrant/www/open-platform/grpc-protobuf/public/index.php:49:
array (size=2)
  'header' => 
    array (size=4)
      'code' => int 200
      'msg' => string 'hi, man' (length=7)
      'mid' => string '1' (length=1)
      'success' => boolean true
  'body' => 
    array (size=2)
      'data' => 
        array (size=3)
          'id' => int 999
          'username' => string 'terry' (length=5)
          'email' => string 'tkstorm@163.com' (length=15)
      'status' => int 200
```


### 相关服务

#### Socket 服务
- 端口监听脚本： 
    - `watch -d -n 0.1 'netstat -an|grep -E "43210|43211"'`
    - `while true;do netstat -an|grep 43210; echo "----------"; sleep 2; done`
- 服务端执行：`php -f server.php`，开启43210端口；
- 客户端执行：`telnet localhost 43210`；

### 待办
- 集成到Laravel框架中；
- 增加Log部分；