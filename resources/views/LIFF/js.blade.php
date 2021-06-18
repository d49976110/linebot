{{-- 引入Vconsole --}}
<script src="https://cdn.jsdelivr.net/npm/vconsole@3.2.0/dist/vconsole.min.js"></script>
<script>
    var vConsole = new VConsole();

</script>


<script>
    var liffID = '1656014340-n0dVloyD';

    liff.init({
        liffId: liffID
    }).then(function() {
        console.log('LIFF init');

        //填入name
        // liff.getProfile().then(
        //     function(profile) {
        //         $('#username').val(profile.displayName);
        //     }
        // );

        //填入userID
        liff.getProfile().then(
            function(profile) {
                $('#userid').val(profile.userId);
            }
        );




    }).catch(function(error) {
        console.log(error);
    });

</script>
