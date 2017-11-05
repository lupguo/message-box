### 相关操作
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

### 待办
- 需要把server端模拟出来，基于sock_stream()来实现；