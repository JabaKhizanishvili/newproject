<?php

class XCards extends XAPI
{
	public function __construct()
	{
		XApiHelper::SetContentType( 'application/json' );

	}

	public function ZKCards()
	{
		$Cards = $this->_LoadCards();
		XApiHelper::SetResponse( 200, 'application/json' );
		$this->_Print( $Cards );
		return;

	}

	public function AllCards()
	{
		$Cards = $this->_LoadAllCards();
		XApiHelper::SetResponse( 200, 'application/json' );
		$this->_Print( $Cards );
		return;

	}

	private function _LoadAllCards()
	{
		$Query = 'select * from ( '
						. 'select '
						. ' pp.permit_id CARD,'
						. ' rwi.device_id id, '
						. ' w.id real_id,'
						. ' w.firstname || \' \' || w.lastname as name '
						. ' from slf_persons w '
						. ' left join rel_person_permit pp on w.id = pp.person '
						. ' left join rel_worker_device_id rwi on rwi.worker = w.id and rwi.permit_id = pp.permit_id '
						. ' where '
						. ' w.active = 1 '
						. ' and nvl(w.permit_id, null) is not null '
						. ' UNION ALL '
						. ' select '
						. ' v.code CARD, '
						. ' rwi.device_id id, '
						. ' v.id real_id,'
						. ' v.lib_title as name'
						. ' from lib_visitors v '
						. ' left join rel_worker_device_id rwi on rwi.worker = v.id '
						. ' where '
						. ' v.active = 1 '
						. ' ) m '
						. ' order by m.id desc '
		;
		$Items = DB::LoadObjectList( $Query, 'PERMIT_ID' );
		$Result = [];
		$K = $this->GetMaxID();
		foreach ( $Items as $Item )
		{
			$ID = C::_( 'ID', $Item );
			$RID = C::_( 'REAL_ID', $Item );
			$CARD = C::_( 'CARD', $Item );
			if ( empty( $ID ) )
			{
				$ID = $K;
				if ( !$this->_SaveRel( $RID, $K, $CARD ) )
				{
					return [];
				}
				$Item->ID = $K;
				$K++;
			}
			$Item->NAME = ucwords( $this->TranslitToLat( C::_( 'NAME', $Item ) ) );
			$Result[] = $Item;
		}
		return $Result;

	}

	public function GetMaxID()
	{
		$Query = 'select max(nvl(r.device_id, 0)) from rel_worker_device_id r ';
		return ((int) DB::LoadResult( $Query )) + 1;

	}

	public function ZKPin()
	{
		$Cards = $this->_LoadPins();
		XApiHelper::SetResponse( 200, 'application/json' );
		$this->_Print( $Cards );
		return;

	}

	protected function _Clean( $ID )
	{
		return preg_replace( '/[^0-9]/', '', $ID );

	}

	private function _LoadCards()
	{
		$Query = 'select '
						. ' pp.permit_id CARD,'
						. ' w.id '
						. ' from slf_persons w '
						. ' left join rel_person_permit pp on w.id = pp.person '
						. ' where '
						. ' w.active = 1 '
						. ' and nvl(pp.permit_id, 0) > 0 '
		;
		return DB::LoadObjectList( $Query, 'PERMIT_ID' );

	}

	public function _LoadPins()
	{
		$Query = 'select '
						. ' pp.permit_id CARD,'
						. ' pp.permit_id PIN,'
						. ' pp.permit_id ID,'
						. ' w.firstname || \' \' || w.lastname USERNAME '
						. ' from slf_persons w '
						. ' left join rel_person_permit pp on w.id = pp.person '
						. ' where '
						. ' w.active = 1 '
						. 'and length(w.permit_id) < 7 '
						. ' and nvl(w.permit_id, 0) > 0 '
		;
		return DB::LoadObjectList( $Query, 'ID' );

		$Items = DB::LoadObjectList( $Query, 'ID' );
		$Result = [];
		foreach ( $Items as $Item )
		{
			$Item->USERNAME = ucwords( $this->TranslitToLat( C::_( 'USERNAME', $Item ) ) );
			$Result[] = $Item;
		}
		$Result[] = $Item;
		return $Result;

	}

	public function _SaveRel( $RID, $K, $CARD )
	{
		$Query = 'insert '
						. ' into rel_worker_device_id '
						. ' ( '
						. 'worker, '
						. ' device_id, '
						. ' permit_id '
						. ' ) '
						. ' values '
						. ' ( '
						. $RID . ' , '
						. $K . ' , '
						. DB::Quote( $CARD )
						. ' ) ';
		return DB::Insert( $Query );

	}

	public function TranslitToLat( $text )
	{
		$str_from = 'ა, ბ, გ, დ, ე, ვ, ზ, თ, ი, კ, ლ, მ, ნ, ო, პ, ჟ, რ, ს, ტ, უ, ფ, ქ, ღ, ყ, შ, ჩ, ც, ძ, წ, ჭ, ხ, ჯ, ჰ';
		$str_to = 'a, b, g, d, e, v, z, t, i, k, l, m, n, o, p, zh, r, s, t, u, f, q, gh, k, sh, ch, c, dz, ts, tc, kh, j, h';

		if ( !empty( $text ) )
		{
			$from = explode( ', ', $str_from );
			$to = explode( ', ', $str_to );
			$trans = str_replace( $from, $to, trim( $text ) );
			return $trans;
		}
		return $text;

	}

}
