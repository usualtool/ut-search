<?php
use usualtool\Search\Search;
use library\UsualToolData\UTData;
$search=new Search();
$data=$search->Run("cms_article","id","title,content","php");
print_r($data);