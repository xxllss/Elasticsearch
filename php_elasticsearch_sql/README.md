# Elasticsearch PHP SQL

php 直接执行sql查询Elasticsearch

添加测试数据

    PUT /test/_doc/1
    {
      "name":"张三",
      "sex":"男",
      "age":20
    }

kibana 可视化查询

    GET /_sql?format=txt
    {
      "query":"select * from demo where name = '张三'"
    }

返回

          age      |     name      |      sex      
    ---------------+---------------+---------------
    20             |张三             |男              




调用脚本查询

    require 'Es.php';
    require 'EsSql.php';
    
    $obj  = new EsSql();
    $sql  = "select * from demo where name = '张三'";
    $rows = $obj->doSql($sql);
    
    var_export($rows);


# 参考

[Elasticsearh sql 官方文档](https://www.elastic.co/cn/what-is/elasticsearch-sql)


