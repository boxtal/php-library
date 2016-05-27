</div>
<script type="text/javascript">
$(document).ready(function() {
    $('[data-toggle="popover"]').popover({ html : true, trigger: 'hover'});
    $('#toogleDebug').click(function (e) {
      $("#debug").toggle();
    })
});
</script>
<style type="text/css">
    body{
        font-size: 13px;
    }
    .popover{
        max-width:500px;
    }
</style>
</body>
</html>