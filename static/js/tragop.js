function resultragop(){
    //lay ket qua chon tu form
	var price = $('#price_final').val();
	var thutuc = parseInt($('#divprofile').val());
	var rateTT = parseInt($('#divtratruoc').val());
	var month = parseInt($('#divmonth').val());

    console.log(price+'-'+thutuc+'-'+rateTT+'-'+month);

	//so sanh voi chuong trinh neu co
	var tienTT = price*(rateTT/100);

	var lsACS  = rateTT > 30 ? 2.2 : 2.45; // lai suat ACS
	if(thutuc == 2){
		var lsHC = 1.66; // ls Homecredit
		var lsHD = 1.56; // ls HD SaiGon
	}else{
		var lsHC   = 3.5; // 0 tuc ko cho tra truoc 0 dong
		var lsHD   = 2.9; // Mobile -> > 10, NB+Tab --> > 20
	}
	var lsFE   = rateTT > 10 ? 1.38 : 2.17;

    xuly(price,rateTT,lsACS,month,'ACS');
    xuly(price,rateTT,lsHC,month, 'HC');
    xuly(price,rateTT,lsHD,month, 'HD');
    xuly(price,rateTT,lsFE,month, 'FE');

}

function xuly(price, tratruoc, laisuat, kyhantra, idDIV){
    TT    = price*(tratruoc/100);
    NCL   = price-TT; // no con lai
    LCKH  = (NCL*(laisuat/100))*kyhantra; // Lai ca ky han
    gopMT = (NCL+LCKH)/kyhantra;
    TongGop = TT+NCL+LCKH;
    cLech  = TongGop-price;

    console.log(TT);
    //format number
    gopMTf = $.number(gopMT, 0, ',', '.' );
    TongGopf = $.number(TongGop, 0, ',', '.' );
    cLechf = $.number(cLech, 0, ',', '.' );
    if(idDIV == 'ACS' && NCL < 3000000) // dieu kien khoan vay ACS > 3tr
    {
        $('#gopMT'+idDIV+'').text('Các tiêu chí bạn chọn chưa thỏa mãn điều kiện '+ idDIV);
        $('#Total'+idDIV+'').text('');
        $('#lech'+idDIV+'').text('');
        $('#ls'+idDIV+'').text('');
    }else if((idDIV != 'ACS' && tratruoc == 0)) // chi co ACS moi cho tra truoc 0đ
    {
        $('#gopMT'+idDIV+'').text('Các tiêu chí bạn chọn chưa thỏa mãn điều kiện '+ idDIV);
        $('#Total'+idDIV+'').text('');
        $('#lech'+idDIV+'').text('');
        $('#ls'+idDIV+'').text('');
    }
    else{
        //show
        $('#gopMT'+idDIV+'').text(gopMTf+'₫');
        $('#Total'+idDIV+'').text(TongGopf+'₫');
        $('#lech'+idDIV+'').text(cLechf+'₫');
        $('#ls'+idDIV+'').text(laisuat+'%');
    }

}