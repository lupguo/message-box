<?php
/**
 * Description...
 *
 * @Author  Terry
 * @Date    2017/11/16 0:31
 */

namespace Rpc;


class Autoloader
{
    /**
     * 开启RPC相关类的自动注册
     *
     */
    public function register() {
        //autoload class for protobuf playload
        spl_autoload_register([$this, 'autoloadClass'], true);
    }
    
    /**
     * 针对Protobuf<Message\Playload>这块的自动加载
     *
     * @param $class
     * @param string $ext
     */
    private function autoloadClass($class, $ext = '.php')
    {
        $logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . $ext;
        $protobufPath = sprintf("%s/%s/%s",__DIR__, 'Protobuf' , $logicalPathPsr4 );
        if (file_exists($protobufPath)) {
            include $protobufPath;
        }
    }
    
    /**
     * 取消RPC相关类的自动注册
     *
     */
    public function unRegister() {
        //unregister class for protobuf playload
        spl_autoload_unregister([$this, 'autoloadClass']);
    }
}