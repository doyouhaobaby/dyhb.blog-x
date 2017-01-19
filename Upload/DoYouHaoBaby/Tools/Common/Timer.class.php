<?php
/* [DoYouHaoBaby!](C)Dianniu From 2010.
	计时器($)*/

class Timer{

	/**
	 * 计时开始
	 *
	 * @access protected
	 * @var int
	 */
	 protected $_nStartTime=0;

	/**
	 * 计时结束
	 *
	 * @access protected
	 * @var int
	 */
	 protected $_nStopTime=0;

	/**
	 * 花费时间
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nTimeSpent=0;

	/**
	 * 开始计算时间
	 *
	 * @access public
	 * @return void
	 */
	public function start(){
		$this->_nStartTime=microtime();
	}

	/**
	 * 计算时间结束
	 *
	 * @access public
	 * @return void
	 */
	public function stop(){
		$this->_nStopTime=microtime();
	}

	/**
	 * 计算运行时间
	 *
	 * @access public
	 * @return void
	 */
	public function spent(){
		if($this->_nTimeSpent){
			return $this->_nTimeSpent;
		}
		else{
			$nStartMicro=substr($this->_nStartTime,0,10);
			$nStartSecond=substr($this->_nStartTime,11,10);
			$nStopMicro=substr($this->_nStopTime,0,10);
			$nStopSecond=substr($this->_nStopTime,11,10);
			$nStart=floatval($nStartMicro)+ $nStartSecond;
			$nStop=floatval($nStopMicro)+ $nStopSecond;
			$this->_nTimeSpent=$nStop - $nStart;
			return substr($this->_nTimeSpent,0,8);
		}
	}

}
