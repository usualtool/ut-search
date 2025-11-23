<?php
namespace usualtool\Search;
use library\UsualToolInc\UTInc;
use TeamTNT\TNTSearch\TNTSearch;
class Search{
    public function __construct(){
        $config=UTInc::GetConfig();
        $this->tnt = new TNTSearch();
        $this->tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => $config["MYSQL_HOST"],
            'database'  => $config["MYSQL_DB"],
            'username'  => $config["MYSQL_USER"],
            'password'  => $config["MYSQL_PASS"],
            'storage'   => __DIR__ . '/index/',
            'stemmer'   => \TeamTNT\TNTSearch\Stemmer\PorterStemmer::class
        ]);
    }
    /**
     * 创建索引文件
     */
	public function CreatIndex($table,$field="*",$index="search"){
		$indexer = $this->tnt->createIndex($index.".index"); 
		$indexer->query("SELECT ".$field." FROM ".$table.";");
		$indexer->run();
	}
    /**
     * 执行搜索
     */
	public function CreatIndex($keyword){
		$this->tnt->selectIndex("articles.index");
        $res = $this->tnt->search($keyword);
	}
}