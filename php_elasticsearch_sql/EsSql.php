<?php
/**
 * Created by PhpStorm.
 * User: xls
 * Date: 2019/12/15
 * Time: 下午2:29
 */

class EsSql extends Es
{
    
    protected static function getUri()
    {
        return '/_sql?format=json';
    }
    
    /**
     * es执行sql
     */
    public static function doSql($sql,$type = 'query',array $key = [])
    {
        
        if ($type == 'query') {
            self::sqlPreproce($sql);
            $match = self::offset($sql);
        }
        $params = [$type => $sql];
        
        $url = self::$url . self::getUri();
        
        $data = self::post($url,$params);
        
        self::checkErr($data);
        
        $key = $key ?: array_column($data['columns'],'name');
        
        array_walk($data['rows'],function (&$val) use ($key) {
            $val = array_combine($key,$val);
        });
        
        if (isset($data['cursor']) && count($data['rows']) >= 1000) {
            $data['rows'] = array_merge($data['rows'],self::doSql($data['cursor'],'cursor',$key));
        }
        if ($type == 'query') {
            return empty($match['offset']) ? $data['rows']
                : array_slice($data['rows'],$match['offset'],$match['limit']);
        }
        return $data['rows'];
    }
    
    /**
     * sql 预处理
     * 1.将双引号转换成单引号
     * 2.当前es版本（7.xx) in 如果是数值型，不能有引号，where 等于则可以
     */
    public static function sqlPreproce(&$sql)
    {
        $sql   = str_replace('"',"'",$sql);
    
        if (stripos($sql,' in') !== false) {
            
            $reg = preg_match('/(into|from)\s+(?<table_name>\w+)/',$sql,$table_info);
            if (!$reg){
                return;
            }
            
            $fields = self::doSql('desc ' . $table_info['table_name']);
            
            foreach ($fields as $field) {
                
                if (!in_array($field['type'],['INTEGER','BIGINT'])) {
                    continue;
                }
                $pattern = "/(?<source>[^\w]" . $field['column'] . "\s+in\s+\(('\d+',?\s?)+\))/i";
                
                if (preg_match($pattern,$sql,$match)) {
                    
                    $dst = str_replace("'",'',$match['source']);
                    
                    $sql = str_replace($match['source'],$dst,$sql);
                }
            }
        }
    }
    
    
    /**
     * es sql 不支持offset，这里单独处理
     */
    private static function offset(&$sql)
    {
        $pattern = '/LIMIT\s+(?<offset>\d+)\s*,\s*(?<limit>\d+)/i';
        if (preg_match($pattern,$sql,$match)) {
            $limit = ' limit ' . ($match['offset'] + $match['limit']);
            $sql   = preg_replace($pattern,$limit,$sql);
            return $match;
        }
        return [];
    }
}

