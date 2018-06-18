<?php
class C {
    public static function who(){
        echo __CLASS__;
    }
}

class A extends C{
    public static function test() {
        static::who(); // 后期静态绑定从这里开始
    }
}

class B extends A {
    public static function who() {
        echo __CLASS__;
    }
}

B::test();