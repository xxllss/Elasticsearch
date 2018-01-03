# elasticsearch集群安装、配置

- **安装最新版java**

	**下载地址（64位）**

		http://javadl.oracle.com/webapps/download/AutoDL?BundleId=220304_d54c1d3a095b4ff2b6607d096fa80163

	**安装**

		rpm -ivh jre-8u131-linux-x64.rpm --prefix=/usr/java/

	**配置JAVA_HOME**

		export JAVA_HOME=/usr/java/jdk1.8.0_131

	>`vim /etc/profile`

	>`source /etc/profile`

- **安装elasticsearch**


	**下载**`
	
		wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-5.4.2.rpm

	**安装**

		rpm -ivh elasticsearch-5.4.2.rpm

		chkconfig --add elasticsearch

		service elasticsearch start

	>这就完成了单节点的安装，通过 `curl localhost:9200`	访问查看得到以下信息：

		@root:# curl localhost:9200
		{
		  "name" : "fBQprn6",
		  "cluster_name" : "elasticsearch",
		  "cluster_uuid" : "XviH-Hv-SGun00-JUR0Bng",
		  "version" : {
		    "number" : "5.4.2",
		    "build_hash" : "929b078",
		    "build_date" : "2017-06-15T02:29:28.122Z",
		    "build_snapshot" : false,
		    "lucene_version" : "6.5.1"
		  },
		  "tagline" : "You Know, for Search"
		}


- **配置elasticsearch集群**


	**配置文件路径**

		/etc/elasticsearch/elasticsearch.yml

	**修改host**

		network.host:192.168.1.22

	>network.host修改为本机ip

	>默认为127.0.0.1或者[::1],更改后elasticsearch会认为你从开发环境转变为生产环境。


	**bootstrap.memory_lock**


		bootstrap.memory_lock: false
		bootstrap.system_call_filter: false


	>组建es**集群**时，需要将锁定内存功能关闭。
	
	>此参数设置为true时，用于锁住内存。因为当jvm开始swapping时es的效率会降低，所以要保证它不swap，可以把ES\_MIN\_MEM和ES\_MAX\_MEM两个环境变量设置成同一个值，并且保证机器有足够的内存分配给es。
	


	**discovery.zen.minimum\_master_nodes**

		discovery.zen.minimum_master_nodes: 1

	>设置这个参数来保证集群中的节点可以知道其它N个有master资格的节点。默认为1（推荐），对于大的集群来说，可以设置大一点的值（2-4），对于三五台节点的集群，必须设为1 。

	

	**discovery.zen.ping.unicast.hosts**

		discovery.zen.ping.unicast.hosts: ["192.168.1.22:9200","92.168.1.23:9200"]

	>新增节点时，提供集群中可能存在和可联系的其他节点的种子列表。不需要全部列出。


- **jvm.options设置**

	**配置文件**	
		
		/etc/elasticsearch/jvm.options

	**jvm heap的大小**

		-Xms256m
		-Xmx256m


	>1.将xms和xmx 最小堆的大小和最大堆的大小设置成一样

	>2.heap越大越好，但是越大垃圾回收的时间越长

	>3.为了保证内核文件缓存有足够的内存，Xmx不应该大于物理内存的50%

	>4.最高不要超过32G(jvm 压缩的极值),最好是26G以内

- **系统设置**

   	**修改内核参数/etc/sysctl.conf**

		vm.swappiness=1

	>操作系统会尽量利用内存，来做文件系统缓存。会占用未使用的应用程序的内存。这会导致部分jvm堆或者可执行页面被交换到磁盘。

	>通过命令关闭系统交换分区`sudo swapoff -a`

	>一般1个G的内存可修改为10， 2个G的可改为5， 甚至是0
	
		vm.max_map_count=262144
	
	>max\_map_count:限制一个进程可以拥有的VMA(虚拟内存区域)的数量。es集群需要该值至少设置为`262144`

	配置完成后执行`sysctl -p` 加载系统参数


	**修改文件描述符`vim /etc/profile`**
	
		ulimit -n 65536

	普通用户需要inux的软硬件限制文件：

		vim /etc/security/limits.conf

		* soft memlock unlimited
		* hard memlock unlimited
		* soft nofile 65536
		* hard nofile 65536

	> memlock 最大锁定内存

    > nofile 是代表最大文件打开数

	>soft 指的是当前系统生效的设置值。hard 表明系统中所能设定的最大值。soft 的限制不能比har 限制高。

	**vi /etc/security/limits.d/90-nproc.conf**

	修改如下内容：

		* soft nproc 1024

	修改为

		* soft nproc 2048

	动态设置

		ulimit -u 2048	

	修改linux系统进程限制，linux下可以通过`ulimit -l unlimited`命令。

- **安装客户端sense插件**

	>在chrome浏览器搜索扩展sense

	![sense插件的使用](https://i.imgur.com/4TJEXcg.png)

- **参考**：

	[在CentOS 7下安装ELK——Elasticsearch](http://webcache.googleusercontent.com/search?q=cache:RgdnLWZE8T4J:www.jianshu.com/p/4d8ea875f4df+&cd=2&hl=zh-CN&ct=clnk&gl=jp)

	[esg官网](https://www.elastic.co/guide/en/elasticsearch/reference/current/system-config.html#dev-vs-prod)

	[修改linux 最大文件限制数 ulimit](http://bian5399.blog.51cto.com/3848702/963662)

	[elasticsearch配置文件](http://blog.csdn.net/sinat_28224453/article/details/51134978)
