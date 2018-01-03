# php客户端调用elasticsearch

1. 在 `https://packagist.org/`搜索`elasticsearch`

	![](http://i.imgur.com/eCjzYEY.png)

1. 通过composer安装：

		composer require elasticsearch/elasticsearch

	>安装composer参考:[http://docs.phpcomposer.com/01-basic-usage.html#Installation](http://docs.phpcomposer.com/01-basic-usage.html#Installation)


1. 连接

		/**
	     * @return \Elasticsearch\Client
	     */
	    public static function getEsInstance(){
	        if (!self::$_es){
	            $hosts = [
	                [
	                'host' => $_SERVER['ES_HOST'],  //192.168.1.22
	                'port' => $_SERVER['ES_PORT'],  //9200
	                'scheme'=>$_SERVER['ES_SCHEME'],//http或者https
	                'user' => $_SERVER['ES_USER'],  //elastic 连接es用户名
	                'pass' => $_SERVER['ES_PWD']    //与用户名对应的密码
	                ]
	                ];
	            self::$_es = Elasticsearch\ClientBuilder::create()
	                ->setHosts($hosts)
	                ->build();       
	        }
	        return  self::$_es;
	    }
	
	>启用用户名密码、设置https需要安装x-pack，我将在之后分享

1. 调用

		class EsSvc extends Svc
	    {   
	         public function testEs(){
	            $params = [
	                'index' => 'testindex',
	                'type' => 'testtype',
	                'id' => '1498579190'
	          ];
	         $response = Svc::getEsInstance()->get($params);
	         return $response;
	    	} 
	    }

	>从索引(index)为testindex、类型(type)为testtype中查找id为1498579190的一条记录

	6.返回结果

	![](http://i.imgur.com/NYjSlBB.png)
	


- **官方文档**

	[官方文档](https://www.elastic.co/guide/en/elasticsearch/reference/current/index.html)
