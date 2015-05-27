






var FCKresizesmin=function(){   

};
FCKresizesmin.prototype.Execute=function(){
}
FCKresizesmin.GetState=function() {
        return FCK_TRISTATE_OFF; 
}
FCKresizesmin.Execute=function() {

      minus();
}


FCKCommands.RegisterCommand( 'resize',  FCKresizesmin ) ;
var oresizeItemmin = new FCKToolbarButton( 'resize', ' уменьшить область для текста' ) ;
oresizeItemmin.IconPath = FCKConfig.PluginsPath + 'resizemin/minus.gif' ;
FCKToolbarItems.RegisterItem( 'resizemin', oresizeItemmin ) ;



