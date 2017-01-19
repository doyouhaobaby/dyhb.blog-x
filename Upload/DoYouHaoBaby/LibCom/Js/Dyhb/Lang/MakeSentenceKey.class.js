/**
 * 语句Md5加密字符串
 *
 * @param string sSentence 语句
 * @returns string
 */
Dyhb.Lang.MakeSentenceKey=function(sSentence) {
	return Dyhb.String.Md5(sSentence);
};
