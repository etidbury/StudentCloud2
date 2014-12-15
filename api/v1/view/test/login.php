
<div class="container">

    <form  id="loginForm" class="form-signin col-md-6 col-lg-6" role="form" style="max-width:300px; margin:0 auto;" action="<?php echo URL."authorize/token";?>" method="post">


        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" name="email" autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="password">
        <div class="checkbox">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Authorize</button>
        </a>
    </form>
    <form class="form-signin col-md-6 col-lg-6" role="form" style="max-width:300px; margin:0 auto;" action="#" method="post" id="customRequestForm">



        <h2 class="form-signin-heading">Custom Request</h2>
        <label>
        Auth:<input type="text" value="30613a5dc7dc4c92482965c0b79516181969b046e8053d2213753a27085d6ee6e8481c081e61d445dd450eae2d7f8b9994d4d179384fef2a5e3c5cf2cbcc7fb7" placeholder="Auth Token" id="auth_token_input"/><hr/>
</label>
        <input type="text" name="1" placeholder="Controller" class="input-get"/>
        <input type="text" name="2" placeholder="Action" class="input-get"/>
        <input type="text" name="3" placeholder="Argument 1" class="input-get"/>
        <input type="text" name="4" placeholder="Argument 2" class="input-get"/>
        <input type="text" name="5" placeholder="Argument 3" class="input-get"/>

        <button class="btn btn-lg btn-primary btn-block" type="submit">GET</button>

        </a>

    </form>

    <form class="form-signin col-md-6 col-lg-6" role="form" style="max-width:300px; margin:0 auto;" action="<?php echo URL;?>drive/files" method="post" id="uploadForm" enctype="multipart/form-data">


        <h2 class="form-signin-heading">Upload file</h2>


        <input type="file" name="file" id="fileToUpload">

        <button class="btn btn-lg btn-primary btn-block" type="submit">Upload</button>
        </a>

    </form>
    <form class="form-signin col-md-6 col-lg-6" role="form" style="max-width:300px; margin:0 auto;" action="<?php echo URL;?>user/account" method="post">


        <h2 class="form-signin-heading">register user</h2>


        <input type="text" name="firstName" value="Edd" />
        <input type="text" name="lastName" value="Tidbury " />

        <input type="text" name="personalEmail" value="tidbury<?php echo rand(0,1000);?>@me.com" />
        <input type="text" name="password" value="yellowdog" />
        <input type="text" name="c_password" value="yellowdog" />
        <label>Accept terms and conditions<input type="checkbox" name="accept_ta" value="1" /></label>



        <button class="btn btn-lg btn-primary btn-block" type="submit">register</button>
        </a>

    </form>

</div> <!-- /container -->
<br/>
<div class="container">


    <pre id="output"></pre>
</div>




<script>

    function output(json) {
        console.log(json);
        var unique=Math.random();

        $('#output').html(unique+"\n\n"+JSON.stringify(json,undefined,2));
    }
    $(document).ready(function() {
       /*$('#loginForm').submit(function(e) {

           e.preventDefault();


           $.post($(this).attr('action'),$(this).serialize()).done(output);


       });*/



        var files;


        $('input[type=file]').on('change', prepareUpload);

        function prepareUpload(event)
        {
            files = event.target.files;
        }

        $('#uploadForm').submit(function(e) {
            e.stopPropagation();
            e.preventDefault();


            var data = new FormData();
            $.each(files, function(key, value)
            {
                data.append(key, value);
            });

            console.log('handled form');
            $.ajax({
                url:$(this).attr('action'),
                type:"POST",
                processData:false,
                contentType:false,
                headers:{"<?php echo AUTH_TOKEN_HEADER;?>":$('#auth_token_input').val()},
                data:data,
                success:output
            })

        });


        $('#customRequestForm').submit(function(e) {

            e.preventDefault();

            var unique=Math.random();
            var urlReq="";
            $('.input-get',this).each(function() {
                if ($(this).val().length>0) urlReq+=$(this).val()+"/";
            });
            output("Loading:"+urlReq+"");

            var nocache = new Date().getTime();


            $.ajax({
                type:'GET',
                url:'<?php echo URL;?>'+urlReq+"&nocache="+nocache,
                headers:{"<?php echo AUTH_TOKEN_HEADER;?>":$('#auth_token_input').val()},

                success:output


            });



        });




    });
</script>