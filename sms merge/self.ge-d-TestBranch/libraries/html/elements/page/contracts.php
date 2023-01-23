<?php

class PageElementContracts extends PageElement
{

    var $_name = 'Contracts';

    public function fetchElement($row, $node, $config)
    {
        $key = trim($node->attributes('key'));
        if(!isset($row->$key))
        {
            return false;
        }
        $contracts = $this->GetContracts($row->$key);
        $return = '<div class="contractsBlock">'
                . '<div class="contractsContainer">'
                . $this->GetHTML($contracts)
                . '</div>'
                . '<div class="cls"></div>'
                . '</div>';
        return $return;
    }

    protected function GetContracts($data)
    {
        $data = trim($data);
        $DataList = $this->cleanData(explode(',', preg_replace('/[^0-9,]/', '', $data)));
        $Contratcs = array();
        if(!empty($DataList))
        {
            $Contratcs = $this->_getContracts($DataList);
        }
        return $Contratcs;
    }

    protected function _getContracts($DataList)
    {
        static $Contracts = array();
        $name = md5(implode(',', $DataList));
        if(!isset($Contracts[$name]))
        {
            $query = 'select r.* , c.lib_title category_name '
                    . ' from ' . DB_SCHEMA . '.contracts_data r '
                    . ' join ' . DB_SCHEMA . '.lib_categories c on r.category = c.id  '
                    . ' where r.id in(' . implode(',', $DataList) . ')'
                    . ' order by c.ordering asc '
            ;
            $Contracts[$name] = DB::LoadObjectList($query);
        }
        return $Contracts[$name];
    }

    protected function cleanData($DataList)
    {
        $Return = array();
        foreach($DataList as $d)
        {
            $d = trim($d);
            if(!empty($d))
            {
                $Return[] = $d;
            }
        }
        return $Return;
    }

    public function GetHTML($Contratcs)
    {
        ob_start();
        $index = 1;
        $GROUP = null;
        if(count($Contratcs))
        {
            foreach($Contratcs as $Contract)
            {
                $IDx[] = $Contract->ID;
                $url = '?option=contractview&id=' . $Contract->ID;
                $uri = URI::getInstance($url);
                $uri->setVar('tmpl', 'modal');
                $uri->setVar('iframe', 'true');
                $uri->setVar('width', '90%');
                $uri->setVar('height', '95%');
                $link = $uri->toString();
                $Name = (empty($Contract->CONTRACT_NAME) ? $Contract->ID : $Contract->CONTRACT_NAME);
                if($GROUP != $Contract->CATEGORY)
                {
                    $GROUP = $Contract->CATEGORY;
                    if($index > 1)
                    {
                        echo '</ol>';
                    }
                    echo '<div class="repoFileItemGroup" >';
                    echo '<div class="repoFileItem_group_name">';
                    echo $Contract->CATEGORY_NAME;
                    echo '</div>';
                    echo '</div>';
                    echo '<div>';
                }
                ?>
                <div class="ContractItem">
                    <div class="ContractItem_name">
                        <a href="<?php echo $link; ?>" rel="viewContract[]" target="_blank">   
                            <?php
                            echo $index . '&nbsp;&nbsp;&nbsp;';
                            echo $Name;
                            ?>
                        </a>
                    </div>
                    <div class="Contracttools">
                        <span class="Contracttool">
                            <a  href="<?php echo $link; ?>" rel="iconviewContract[]" target="_blank">   
                                <img src="templates/images/view.png" alt="View" />
                            </a>
                        </span>
                    </div>
                    <div class="cls"></div>
                </div>
                <?php
                ++$index;
            }
            echo '</div>';
        }
        $dat = ob_get_contents();
        ob_end_clean();
        return $dat;
    }

}
