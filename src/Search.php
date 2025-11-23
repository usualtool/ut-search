<?php
namespace usualtool\Search;
use library\UsualToolInc\UTInc;
use usualtool\Search\SplitWord;
use TeamTNT\TNTSearch\TNTSearch;
use library\UsualToolData\UTData;
class Search{
    public function __construct(){
        $config = UTInc::GetConfig();
        $this->tnt = new TNTSearch();
        $this->tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => $config["MYSQL_HOST"],
            'database'  => $config["MYSQL_DB"],
            'username'  => $config["MYSQL_USER"],
            'password'  => $config["MYSQL_PASS"],
            'storage'   => __DIR__ . '/cache/',
            'asYouType' => false,
            'fuzziness' => false,
            'stemmer'   => null
        ]);
    }
    public function Run($table,$key,$field,$keyword){
        $cache_name = $table.".cache";
        $cache_file = __DIR__."/cache/".$cache_name;
        if(!file_exists($cache_file)){
            if (!is_dir(__DIR__ . '/cache')) {
                mkdir(__DIR__ . '/cache', 0755, true);
            }
            ob_start();
            $index = $this->tnt->createIndex($cache_name);
            $index->setTokenizer(new SplitWord());
            $fields = explode(',', $field);
            $selectFields = "`{$key}` AS id";
            if (!empty($fields)) {
                $textParts = [];
                foreach ($fields as $f) {
                    $f = trim($f);
                    if ($f !== '') {
                        $textParts[] = "`{$f}`";
                    }
                }
                if (!empty($textParts)) {
                    $ifnullParts = array_map(function($col) {
                        return "IFNULL({$col}, '')";
                    }, $textParts);
                    $concatExpr = "CONCAT(" . implode(", ' ', ", $ifnullParts) . ")";
                    $selectFields .= ", {$concatExpr} AS text";
                }
            }
            $index->query("SELECT {$selectFields} FROM {$table}");
            $index->run();
            ob_end_clean();
        }
        $this->tnt->selectIndex($cache_name);
        $this->tnt->setTokenizer(new SplitWord());
        $data=$this->tnt->search($keyword);
        if(isset($data['ids'])) {
            $ids=$data['ids'];
            return $this->SearchData($table,$key,$ids);
        }else{
            return "No Data";
        }
    }
    public function SearchData($table,$key,$ids){
        $data = array();
        foreach($ids as $rows){
            $row = UTData::QueryData($table,"",$key."='$rows'")["querydata"][0];
            $data[] = $row;
        }
        return $data;
    }
}