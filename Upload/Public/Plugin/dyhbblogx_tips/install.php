<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	��Ϣ��ʾ�����װ����($)*/

!defined('DYHB_PATH') && exit;

/**
// �����Ϊ��Ҫ���ݿ����������ʼ��������ʹ��
// ����������Ҫ��ʼ��һЩ���ݣ������ɾ�����ļ�
$sSql=<<<EOF

DROP TABLE IF EXISTS dyhbblogx_hello;
CREATE TABLE dyhbblogx_hello (
  `test_id` int(10) NOT NULL auto_increment COMMENT '����ID',
  `test_value` varchar(50) character set utf8 NOT NULL COMMENT '����Ч��',
  PRIMARY KEY  (`test_id`)
) TYPE=MyISAM;

EOF;

$this->runQuery($sSql);
*/

$bFinish=TRUE;
