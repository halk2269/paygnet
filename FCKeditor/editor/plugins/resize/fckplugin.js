var FCKresizes=function(){   
};
FCKresizes.prototype.Execute=function(){
}
FCKresizes.GetState=function() {
        return FCK_TRISTATE_OFF; 
}
FCKresizes.Execute=function() {
      plus();
}

FCKCommands.RegisterCommand( 'resize',  FCKresizes ) ;
var oresizeItem = new FCKToolbarButton( 'resize', ' увеличить область для текста' ) ;
oresizeItem.IconPath = FCKConfig.PluginsPath + 'resize/plus.gif' ;
FCKToolbarItems.RegisterItem( 'resize', oresizeItem ) ;