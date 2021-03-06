layui.define(['layer', 'form'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var form = layui.form;


	// 检查更新
    function checkUpdate() {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/admin/index/updatecheckajax",
            timeout: 10000, //ajax请求超时时间10s
            data: {"csrf_token": TOKEN,'method':"updatecheck"}, //post数据
            success: function (res, textStatus) {
                //从服务器得到数据，显示数据并继续查询
				if (res.code == 1) {
					var html = '<div class="mod-content" style="text-align: center;padding: 20px 100px;"><p>哇!有更新啦！</p></div>';
					layer.open({
						type: 1
						,title: "更新提示"
						,offset: 'auto'
						,id: 'layerPayone' //防止重复弹出
						,content: html
						,btn: ['去GitHub','下载ZIP包', '残忍拒绝']
						,btnAlign: 'c' //按钮居中
						,shade: 0.8 //不显示遮罩
						,success: function(layero){
						  var btn = layero.find('.layui-layer-btn');
						  btn.find('.layui-layer-btn0').attr({
							href: res.data.url
							,target: '_blank'
						  });
						  btn.find('.layui-layer-btn1').attr({
							href: res.data.zip
							,target: '_blank'
						  });
						} 
					});
				}
            },
        });
    }

	checkUpdate();
	exports('adminindex',null)
});