function label_print_70x20(code,customerCode,categoryName,intCopies){
    if(!LODOP){
        LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
    }
    var printerNo = getPrinterNo('70x20');
    if (printerNo === null) {
        printerSetup();
        return;
    }
    LODOP.PRINT_INITA("0cm","0cm","6.96cm","2.17cm",code);
    LODOP.ADD_PRINT_BARCODE("1.1mm","4.8mm","60.1mm","12.2mm","128B",code);
    LODOP.SET_PRINT_STYLEA(0,"Horient",2);
    LODOP.ADD_PRINT_TEXT("14.8mm","0.69cm","29.9mm","5mm",customerCode);
    LODOP.ADD_PRINT_TEXT("1.46cm","2.04cm","4.65cm","0.53cm",categoryName);
    LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
    LODOP.SET_PRINT_STYLEA(0,"SpacePatch",1);
    LODOP.SET_PRINT_STYLEA(0,"AlignJustify",1);
    LODOP.SET_PRINT_STYLEA(0,"Horient",1);
    LODOP.SET_PRINT_COPIES(intCopies);
    LODOP.SET_LICENSES("","196421061011095010011211256128","688858710010010811411756128900","");
    LODOP.PRINT();
}


/**
 * 70*20
 * @param code
 * @param intCopies
 */
function printPackingLabel(code,intCopies){
    if(!LODOP){
        LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
    }
    var printerNo = getPrinterNo('70x20');
    if (printerNo === null) {
        printerSetup();
        return;
    }
    LODOP.PRINT_INITA("0cm","0cm","6.96cm","2.17cm",code);
    LODOP.ADD_PRINT_BARCODE("1.1mm","4.8mm","60.1mm","14.2mm","128B",code);
    LODOP.SET_PRINT_STYLEA(0,"Horient",2);
    LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
    LODOP.SET_PRINT_STYLEA(0,"SpacePatch",1);
    LODOP.SET_PRINT_STYLEA(0,"AlignJustify",1);
    LODOP.SET_PRINT_STYLEA(0,"Horient",1);
    LODOP.SET_PRINT_COPIES(intCopies);
    LODOP.SET_LICENSES("","196421061011095010011211256128","688858710010010811411756128900","");
    LODOP.PRINT();
}

/**
 * 70*20
 * @desc 包袋号
 * @param code
 * @param intCopies
 */
function printBagLabel(code,intCopies){
    if(!LODOP){
        LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
    }
    var printerNo = getPrinterNo('70x20');
    if (printerNo === null) {
        printerSetup();
        return;
    }
    LODOP.PRINT_INITA("0cm","0cm","6.96cm","2.17cm",code);
    LODOP.ADD_PRINT_BARCODE("1.1mm","4.8mm","60.1mm","14.2mm","128B",code);
    LODOP.SET_PRINT_STYLEA(0,"Horient",2);
    LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
    LODOP.SET_PRINT_STYLEA(0,"SpacePatch",1);
    LODOP.SET_PRINT_STYLEA(0,"AlignJustify",1);
    LODOP.SET_PRINT_STYLEA(0,"Horient",1);
    LODOP.SET_PRINT_COPIES(intCopies);
    LODOP.SET_LICENSES("","196421061011095010011211256128","688858710010010811411756128900","");
    LODOP.PRINT();
}