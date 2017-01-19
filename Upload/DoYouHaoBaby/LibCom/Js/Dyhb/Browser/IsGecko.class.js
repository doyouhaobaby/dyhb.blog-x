/**
 * @property isGecko 判断是否为gecko内核
 * 
 * 目前世界上主要有来自四个不同机构的四种的Web浏览器内核，分别是和IE搭配的Trident、和Firefox搭配的Gecko、和Safari搭配的WebKit以及跟Opera搭配的Presto。
 * Gecko内核由Mozilla公司开发，是一种快速而强大的格式引擎技术。它能够对HTML 4.0、CSS 1/2、W3C、XML 1.0、RDF和JavaScript等开放的Internet标准提供强有
 * 力的支持，不仅可以准确地显示各种类型的网页文档、表单、JavaScript和Applets程序，还可以用来书写用户界面，实现在屏幕上显示滚动条和菜单栏等效果。
 * 它是Mozilla系列（Firefox就是Mozilla公司的产品）以及Netscape 6.x以后版本的底层技术基石。 
 * Firefox的成功归根到底是Gecko内核的功劳。它安全、快速和稳定，让大家在对IE的安全性慢慢失去信心的时候，看到了希望。
 *
 */
Dyhb.Browser.IsGecko=/gecko/i.test(Dyhb.Browser.UserAgent) && !/like gecko/i.test(Dyhb.Browser.UserAgent);
