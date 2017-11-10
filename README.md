### gRPC+protobuf的调试
> protobuf： 谷歌出品的rpc序列化+反序列化的工具，能较高性能的数据交换，与thrift、avro这类属于同类型产品；
> 同时客户端和服务端都遵循IDL约定，同时需要proto编译工具的提前支持。

> gRPC: 谷歌出品的rpc解决方案，默认是依托于protobuf协议；

### 准备工作
1. 安装protoc编译器：https://github.com/google/protobuf
2. 进入到根目录，执行相关protoc文件的编译：
   ```
   protoc -I idl_src/ --php_out=./php_out search.proto
   ```
3. 配置web server: 
    ```
    # rpc protobuf
    192.168.10.10  rpc-protobuf.net 
    ```
4. 配置vagrant的`Homestead.yaml`配置文件，支持vhost。
5. 做好xdebug的相关调试

### 相关服务

#### Socket 服务
- 端口监听脚本： 
    - `watch -d -n 0.1 'netstat -an|grep -E "43210|43211"'`
    - `while true;do netstat -an|grep 43210; echo "----------"; sleep 2; done`
- 服务端执行：`php -f server.php`，开启43210端口；
- 客户端执行：`telnet localhost 43210`；

### 待办
- 需要把server端模拟出来，基于sock_stream()来实现，用于server端的接收调试；
