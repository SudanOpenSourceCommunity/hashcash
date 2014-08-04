<?php

namespace TheFox\Pow;

#use RuntimeException;
#use InvalidArgumentException;
#use DateTime;

use TheFox\Storage\YamlStorage;

class HashcashDb extends YamlStorage{
	
	private $hashcashsId = 0;
	private $hashcashs = array();
	
	public function __construct($filePath = null){
		parent::__construct($filePath);
		
		$this->data['timeCreated'] = time();
	}
	
	public function save(){
		#fwrite(STDOUT, __CLASS__.'->'.__FUNCTION__.''."\n");
		
		$this->data['hashcashs'] = array();
		foreach($this->hashcashs as $hashcashId => $hashcash){
			#fwrite(STDOUT, __CLASS__.'->'.__FUNCTION__.': '.$hashcashId."\n");
			
			$hashcashAr = array();
			$hashcashAr['id'] = $hashcashId;
			$hashcashAr['stamp'] = $hashcash->getStamp();
			
			if($hashcash->verify()){
				$this->data['hashcashs'][$hashcashId] = $hashcashAr;
			}
		}
		
		$rv = parent::save();
		unset($this->data['hashcashs']);
		
		return $rv;
	}
	
	public function load(){
		#fwrite(STDOUT, __CLASS__.'->'.__FUNCTION__.''."\n");
		
		if(parent::load()){
			if(isset($this->data['hashcashs']) && $this->data['hashcashs']){
				foreach($this->data['hashcashs'] as $hashcashId => $hashcashAr){
					$this->hashcashsId = $hashcashId;
					#fwrite(STDOUT, __CLASS__.'->'.__FUNCTION__.': '.$this->hashcashsId."\n");
					
					$hashcash = new Hashcash();
					if($hashcash->verify($hashcashAr['stamp'])){
						$this->hashcashs[$hashcashId] = $hashcash;
					}
				}
			}
			unset($this->data['hashcashs']);
			
			return true;
		}
		
		return false;
	}
	
	public function hasDoublespend(Hashcash $hashcash){
		return in_array($hashcash, $this->hashcashs);
	}
	
	public function addHashcash(Hashcash $hashcash){
		if(!$this->hasDoublespend($hashcash)){
			$this->hashcashsId++;
			$this->hashcashs[$this->hashcashsId] = $hashcash;
			$this->setDataChanged(true);
			
			return true;
		}
		
		return false;
	}
	
	public function getHashcashs(){
		return $this->hashcashs;
	}
	
}
