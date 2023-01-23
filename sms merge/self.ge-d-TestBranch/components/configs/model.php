<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class ConfigsModel extends Model
{
	public function getList()
	{
		$Files = Folder::files( X_PATH_BASE . DS . 'components', 'configuration\.xml', true, true );
		$Return = $this->getReturn();
        $Menu = $this->GetMenu();
        $K = 999;
		foreach ( $Files as $File )
		{
			$XMLDoc = Helper::loadXMLFile( $File );
			$Name = $XMLDoc->getElementByPath( 'params' )->attributes( 'name' );
			$AltMenu = $XMLDoc->getElementByPath( 'params' )->attributes( 'menu' );
			$Ordering = $XMLDoc->getElementByPath( 'params' )->attributes( 'ordering', $K );
			$Option = basename( dirname( $File ) );
			while ( isset( $Return->items[$Ordering] ) )
			{
				$Ordering += 10;
			}
			$Return->items [$Ordering] = array(
					'ID' => $Option,
					'NAME' => Text::_( $Name ),
					'OPTION' => $Option,
					'MENU' => C::_( $Option . '.LIB_TITLE', $Menu, Text::_( $AltMenu ) ),
					'UID' => $Ordering
			);
			$K++;
		}
		ksort( $Return->items );
        $Return->name = trim( Request::getState( $this->_space, 'name', '' ) );
        $collect = [];
        $data = $Return->items;
        foreach ( $data as $item )
        {
            if ( $Return->name && preg_match( '/' . $Return->name . '/i', $item['NAME'] ) )
            {
                $collect[] = $item;
            }
        }
        if ( count( $collect ) )
        {
            $Return->items = $collect;
        }
		return $Return;

	}

	public function GetMenu()
	{
		$Menu = MenuConfig::getInstance();
		$MItems = $Menu->getUserMenu( false );
		$R = array();
		foreach ( $MItems as $Item )
		{
			$R[C::_( 'LIB_OPTION', $Item )] = $Item;
		}
		return $R;

	}

}
