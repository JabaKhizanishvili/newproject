<?php

class PageElementFiles extends PageElement
{

    var $_name = 'Files';

    public function fetchElement($row, $node, $config)
    {
        $key = trim($node->attributes('key'));
        if(!isset($row->{$key}))
        {
            return false;
        }
        $Files = $this->GetFiles($row->{$key});
        $return = '<div class="contractfilesblock">'
                . '<div class="contractFilecontainer">'
                . $this->GetHtml($Files)
                . '</div>'
                . '<div class="cls"></div>'
                . '</div>';
        return $return;
    }

    public function GetFiles($data)
    {
        $data = trim($data);
        $DataList = $this->cleanData(explode(',', preg_replace('/[^0-9,]/', '', $data)));
        $Files = array();
        if(!empty($DataList))
        {
            $Files = $this->_getRepoFiles($DataList);
        }
        return $Files;
    }

    protected function _getRepoFiles($DataList)
    {
        static $files = array();
        $name = md5(implode(',', $DataList));
        if(!isset($files[$name]))
        {
            $query = 'select r.*, f.lib_title category '
                    . ' from ' . DB_SCHEMA . '.repos r '
                    . ' join ' . DB_SCHEMA . '.lib_filescats f on r.filescats = f.id  '
                    . ' where r.id in(' . implode(',', $DataList) . ')'
                    . ' order by f.ordering asc '
            ;
            $files[$name] = DB::LoadObjectList($query);
        }
        return $files[$name];
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

    protected function GetHtml($Files)
    {
        ob_start();
        $index = 1;
        $GROUP = null;
        if(count($Files))
        {
            $hash = substr(md5(microtime()), rand(1, 25), 5);
            foreach($Files as $File)
            {
                $url = '?option=repofile&id=' . $File->ID;
                $uri = URI::getInstance($url);
                $uri->setVar('tmpl', 'modal');
                $uri->setVar('iframe', 'true');
                $uri->setVar('width', '90%');
                $uri->setVar('height', '95%');
                $link = $uri->toString();
                if($GROUP != $File->FILESCATS)
                {
                    $GROUP = $File->FILESCATS;
                    if($index > 1)
                    {
                        echo '</div>';
                    }
                    echo '<div class="repoFileItemGroup" >';
                    echo '<div class="repoFileItem_group_name">';
                    echo $File->CATEGORY;
                    echo '</div>';
                    echo '</div>';
                    echo '<div>';
                }
                ?>
                <div class="repoFileItem">
                    <div class="repoFileItem_name">
                        <a href="<?php echo $link; ?>" rel="viewrepofile[<?php echo $hash; ?>]" target="_blank" title="<?php echo $File->CATEGORY; ?>">   
                            <?php
                            echo $index . '&nbsp;&nbsp;&nbsp;';
                            echo $File->FILE_NAME;
                            ?>
                        </a>
                    </div>
                    <div class="repoFiletools">
                        <span class="repoFiletool">
                            <a  href="<?php echo $link; ?>" rel="iconviewrepofile[<?php echo $hash; ?>]" target="_blank">   
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
