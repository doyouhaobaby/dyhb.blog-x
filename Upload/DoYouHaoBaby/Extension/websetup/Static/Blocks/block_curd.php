<?php
/**

 //  [Websetup!] 图像界面工具
 //  +---------------------------------------------------------------------
 //
 //  “Copyright”
 //  +---------------------------------------------------------------------
 //  | (C) 2010 - 2011 http://doyouhaobaby.net All rights reserved.
 //  | This is not a free software, use is subject to license terms
 //  +---------------------------------------------------------------------
 //
 //  “About This File”
 //  +---------------------------------------------------------------------
 //  | websetup CURD 基本代码块
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

/**
 * CURD 代码块
 */
function block_curd($sTable){
	$sModuleName=ucfirst($sTable);
	return array(
		// curd:create代码块
		'create'=>"// 自动化curd:create方法体代码
		\$sSeccode=G::getGpc('seccode','G');
		// 判断验证码是否正确
		// 并且尝试捕获错误
		// UserModel::M()->checkSeccode(\$sSeccode);
		// if(UserModel::M()->isBehaviorError()){
		// \$this->E(UserModel::M()->getBehaviorErrorMessage());
		// }
		\$o{$sModuleName}=new {$sModuleName}Model(); 
		\$bResult=\$o{$sModuleName}->save();
		if(!\$o{$sModuleName}->isError()){
			\$this->S('数据保存成功！');	
		}
		else{
			\$this->E(\$o{$sModuleName}->getErrorMessage()); 
		}",
		// curd:ajax_create
		'ajax_create'=>"// 自动化curd:ajax_create方法体代码
		// 判断验证码是否正确
		// 并且尝试捕获错误
		// UserModel::M()->checkSeccode(\$sSeccode);
		// if(UserModel::M()->isBehaviorError()){
		// \$this->E(UserModel::M()->getBehaviorErrorMessage());
		// }
		\$o{$sModuleName}=new {$sModuleName}Model(); 
		\$bResult=\$o{$sModuleName}->save();
		if(!\$o{$sModuleName}->isError()){
			\$arrData=\$o{$sModuleName}->toArray();
			\$arrData['dateline']=date('Y-m-d H:i:s',\$arrData['dateline']); 
			\$arrData['{$sTable}_content']=\$arrData['{$sTable}_content']; 
			\$this->A(\$arrData,'表单数据保存成功！',1); 
		}
		else{ 
			\$this->E(\$o{$sModuleName}->getErrorMessage()); 
		} ",
		// curd:form_create
		'form_create'=>"// 自动化curd:ajax_form方法体代码
		// 判断验证码是否正确
		// 并且尝试捕获错误
		// UserModel::M()->checkSeccode(\$sSeccode);
		// if(UserModel::M()->isBehaviorError()){
		// \$this->E(UserModel::M()->getBehaviorErrorMessage());
		// }
		\$o{$sModuleName}=new {$sModuleName}Model(); 
		\$bResult=\$o{$sModuleName}->save();
		if(!\$o{$sModuleName}->isError()){
			\$this->E(\$o{$sModuleName}->getErrorMessage()); 
		}
		else{
			header(\"Content-Type:text/html; charset=utf-8\"); 
			exit(\$o{$sModuleName}->getErrorMessage().' [ <a href=\"javascript:history.back()\">返 回</A> ]','0'); 
		} ",
		// curd:read
		'read'=>"// 自动化curd:read方法体代码
		// 判断验证码是否正确
		// 并且尝试捕获错误
		// UserModel::M()->checkSeccode(\$sSeccode);
		// if(UserModel::M()->isBehaviorError()){
		// \$this->E(UserModel::M()->getBehaviorErrorMessage());
		// }
		\$arrList={$sModuleName}Model::F()
					->order('`{$sTable}_id` DESC')
					->all()
					->query();
		\$this->assign('arrList',\$arrList);
		\$this->display();",
		// curd:delete
		'delete'=>"// 自动化curd:delete
		\$sId=G::getGpc('id','G');
		if(!\$sId){
			\$o{$sModuleName}Meta={$sModuleName}Model::M();
			\$sPk=reset(\$o{$sModuleName}Meta->_arrIdName);
			// 执行删除
			\$o{$sModuleName}Meta->deleteWhere(array(\$sPk=> array('in',\$sId)));
			// 开始模型Meta中的错误，如果没有那么路过
			if(\$o{$sModuleName}Meta->isError()){
				\$this->E(\$o{$sModuleName}Meta->getErrorMessage());
			}
			else{
				\$this->A('',G::L('删除记录成功！'),1);
			}
		}
		else{ 
			\$this->E('删除项不存在！'); 
		} ",
	
		// curd:update
		'update'=>"// 自动化curd:update方法体代码
		\$nId=G::getGpc('id','G');
		\$o{$sModuleName}={$sModuleName}Model::F(\"".MODULE_NAME."_id=?\",\$nId)->query();
		\$o{$sModuleName}->save('update');
		if(\$o{$sModuleName}->getErrorMessage()=='zero-effect'){
			\$this->E(G::L('你没有修改数据！'));
		}
		// 开始捕获模型中的错误
		if(!\$o{$sModuleName}->isError()){
			\$this->S(G::L('数据更新成功！'));
		}
		else{
			\$this->E(\$o{$sModuleName}->getErrorMessage());
		}
		",
		// curd:edit
		'edit'=>"// 自动化curd:edit方法体代码
		\$nId=G::getGpc('id','G');
		// 判断是否存在编辑项
		if(!empty(\$nId)){
			\$o{$sModuleName}={$sModuleName}Model::F(\"".MODULE_NAME."_id=?\",\$nId)->query();
			if(\$o{$sTable}){
				\$this->assign('arrValue',\$o{$sModuleName}->toArray());
				\$this->assign('nId',\$nId);
				\$this->display(\"".MODULE_NAME."+add\");
			}
			else{
				\$this->E(G::L('数据库中并不存在该项，或许它已经被删除了！'));
			}
		}
		else{
			\$this->E(G::L('编辑项不存在！'));
		}
		",
		// curd:unique
		'unique'=>"// 自动化curd:unique方法体代码
		\$nId=G::getGpc('id','G');
		\$s{$sModuleName}Name=G::getGpc('{$sTable}_name','P');
		\$s{$sModuleName}Info='';
		if(\$s{$sModuleName}Name){
			\$arr{$sModuleName}={$sModuleName}Model::F('{$sTable}_id=?',$nId)->asArray()->getOne();
			\$s{$sModuleName}Info=trim(\$arr{$sModuleName}['{$sTable}_name']);
		}
		if(\$s{$sModuleName}Name !=\$s{$sModuleName}Info){
			\$arrResult=self::F()->getBy{$sTable}_name(\$s{$sModuleName}Name)->toArray();
			if(!empty(\$arrResult['{$sTable}_id'])){
				return false;
			}
			else{
				return true;
			}
		}
		return true;
		",
		// curd:check
		'check'=>"// 自动化curd:check方法体代码
		G::seccode();
		",
	);

}
