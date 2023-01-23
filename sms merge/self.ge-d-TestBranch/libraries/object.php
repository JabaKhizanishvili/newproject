<?php
defined( 'DS' ) or die( 'Restricted Access!' );

/**
 * Description of base
 *
 * @author teimuraz.kevlishvili
 */
class XObject
{
	public static function GetInstance( $instanceName = null )
	{
		$ClassName = get_called_class();
		if ( !$instanceName )
		{
			$instanceName = $ClassName;
		}
		static $instance = array();
		if ( !isset( $instance[$instanceName] ) )
		{
			$instance[$instanceName] = new $ClassName( $instanceName );
		}

		return $instance[$instanceName];

	}

	/**
	 * Sets a default value if not already assigned
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $Default   The default value.
	 *
	 * @return  mixed
	 *
	 * @since   11.1
	 */
	public function Def( $property, $Default = null )
	{
		$value = $this->get( $property, $Default );

		return $this->set( $property, $value );

	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $Default   The default value.
	 *
	 * @return  mixed    The value of the property.
	 *
	 * @since   11.1
	 *
	 * @see     XObject::getProperties()
	 */
	public function Get( $Property, $Default = null )
	{
		$Properties = $this->GetProperties( false );
		return C::_( $Property, $Properties, $Default );

	}

	/**
	 * Returns an associative array of object properties.
	 *
	 * @param   boolean  $public  If true, returns only the public properties.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 *
	 * @see     XObject::get()
	 */
	public function GetProperties( $public = true )
	{
		$vars = get_object_vars( $this );

		if ( $public )
		{
			foreach ( $vars as $Key => $value )
			{
				if ( '_' == substr( $Key, 0, 1 ) )
				{
					unset( $vars[$Key] );
				}
			}
		}

		return $vars;

	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 *
	 * @return  mixed  Previous value of the property.
	 *
	 * @since   11.1
	 */
	public function Set( $property, $value = null )
	{
		$previous = isset( $this->{$property} ) ? $this->{$property} : null;
		$this->{$property} = $value;

		return $previous;

	}

	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param   mixed  $properties  Either an associative array or another object.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 *
	 * @see     XObject::set()
	 */
	public function SetProperties( $properties )
	{
		if ( is_array( $properties ) || is_object( $properties ) )
		{
			foreach ( (array) $properties as $K => $v )
			{
				// Use the set function which might be overridden.
				$this->set( $K, $v );
			}

			return true;
		}

		return false;

	}

	public function Reset( $Public = true )
	{
		$Data = array_keys( $this->GetProperties( $Public ) );
		foreach ( $Data as $Key )
		{
			$this->Set( $Key, null );
		}

	}

}
