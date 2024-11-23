<?php
namespace open20\elasticsearch\models;

class ItemCondition extends BaseItemCondition
{    
    private $field;
    private $condition;
    private $operation = 'must';
    
    
    public function getField() 
    {
        return $this->field;
    }

    public function getCondition() 
    {
        return $this->condition;
    }

    public function setField($field)
    {
        $this->field = $field;
    }

    public function setCondition($condition)
    {
        $this->condition = $this->escapeElasticSearchReservedChars($condition);
    }
   
    public function getOperation() 
    {
        return $this->operation;
    }

    public function setOperation($operation) 
    {
        $this->operation = $operation;
    }
    
    /**
     * 
     * @return type
     */
    public function toArray()
    {
        $condition = [$this->field => $this->condition];
        if(!empty($this->analyzer))
        {
            $condition["analyzer"] = $this->analyzer;
        }
        if(count($this->fields))
        {
            $condition["fields"] = $this->fields;
        }
        return [$this->operation => $condition];
    }
}
