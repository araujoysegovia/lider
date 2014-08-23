<?php
namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class MainMongoRepository extends DocumentRepository
{
	public function consultDocument($document, $dm, array $filter = null, $start = null, $limit = null)
    {
        $sql = $this->createQueryBuilder($document);
        $md = $dm->getClassMetadata("LiderBundle:".$document);
        $fieldMappings = $md->fieldMappings;
        $query = null;
        $val1 = null;
        $val2 = null;
        if(!is_null($filter))
        {
            foreach($filter as $value)
            {
                $property = $value['property'];
                $val = $value['value'];
                $operator = $value['operator'];
                $fmType = $fieldMappings[$property]['type'];
                if($fmType == "date")
                {
                    if(is_array($val))
                    {
                        $first = new \MongoDate(strtotime($val[0]));
                        $second = new \MongoDate(strtotime($val[1]." 23:59:59"));
                    }
                    else
                    {
                        if($operator == "<=" || $operator == ">")
                        {
                            $val = new \MongoDate(strtotime($val." 23:59:59"));
                        }
                        elseif($operator == "=")
                        {
                            $val1 = new \MongoDate(strtotime($val));
                            $val2 = new \MongoDate(strtotime($val." 23:59.59"));
                        }
                        else
                        {
                            $val = new \MongoDate(strtotime($val));
                        }
                    }
                }
                elseif($fmType == "int")
                {
                    if(is_array($val))
                    {
                        $first = intval($val[0]);
                        $second = intval($val[1]);
                    }
                    else
                    {
                        $val = intval($val);
                    }
                }
                else
                {
                }
                switch($operator)
                {
                    case "=":
                        if($fmType == "date")
                        {
                            $query = $sql->field($property)->range($val1, $val2);
                        }
                        else
                        {
                            switch($val)
                            {
                                case "true":
                                    $query = $sql->field($property)->equals(true);
                                    break;
                                case "false":
                                    $query = $sql->field($property)->equals(false);
                                    break;
                                default:
                                    $query = $sql->field($property)->equals($val);
                                    break;
                            }
                        }
                        break;
                    case ">":
                        $query = $sql->field($property)->gt($val);
                        break;
                    case "<":
                        $query = $sql->field($property)->lt($val);
                        break;
                    case ">=":
                        $query = $sql->field($property)->gte($val);
                        break;
                    case "<=":
                        $query = $sql->field($property)->lte($val);
                        break;
                    case "has":
                        $query = $sql->field($property)->equals(new \MongoRegex('/.*'.$val.'.*/i'));
                        break;
                    case "equal":
                        switch($val)
                        {
                            case "true":
                                $query = $sql->field($property)->equals(true);
                                break;
                            case "false":
                                $query = $sql->field($property)->equals(false);
                                break;
                            default:
                                $query = $sql->field($property)->equals($val);
                                break;
                        }
                        break;
                    case "start_with":
                        $query = $sql->field($property)->equals(new \MongoRegex('/^'.$val.'/i'));
                        break;
                    case "end_with":
                        $query = $sql->field($property)->equals(new \MongoRegex('/^(.)*('.$val.')$/i'));
                        break;
                    case "between":
                        $sql->field($property)->range($first, $second);
                        break;
                    case "empty":
                        $query = $sql->field($property)->equals("");
                        break;
                }
            }
        }
        if($start)
        {
            $query = $sql->skip($start);
        }
        if($limit)
        {
            $query = $sql->limit($limit);
        }
        $query = $sql->getQuery()
        ->execute();

        return $query;
    }

    public function searchExpireSession($date)
    {
        $searchDate = new \MongoDate(strtotime($date));
        $dm = $this->getDocumentManager();
        $query = $dm->createQueryBuilder('LiderBundle:Session')
                    ->field('enabled')->equals(true)
                    ->field("last")->lte($searchDate)
                    ->getQuery()
                    ->execute();
        return $query;
    }
}
?>