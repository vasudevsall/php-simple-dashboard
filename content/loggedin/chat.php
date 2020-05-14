<?php
$page_title = "Chat Room";
$page_name = "chat";
include '../include/header.php';
?>
        <img src="../../images/mirage-uploading.png" alt="message-illustration" class="message-img">
        <img src="../../images/mirage-message-sent.png" alt="message-illustration" class="message-img-2">
        <div class="chat_wrapper">
            <div id="chat"></div>
            <form method="POST" id="messageFrm" class="messg-form">
                <textarea name="message" cols="20" rows="5" class="textarea" placeholder="Type your message here!"></textarea>
                <input type="submit" value="Send" class="submit-btn">
            </form>
        </div>
    </div>

    <script>
        LoadChat();

        setInterval(function () {
            LoadChat();
        }, 1000);

        function LoadChat() {
            $.post('handlers/messages.php?action=getMessages', function(response){

                var scrollpos = $('#chat').scrollTop();
                scrollpos = parseInt(scrollpos) + 520;
                var scrollHeight = $('#chat').prop('scrollHeight');

                $('#chat').html(response);
                if(scrollpos >= scrollHeight)
                    $('#chat').scrollTop($('#chat').prop('scrollHeight'));
            });
        }

        $('.textarea').keyup(function (e) {
            if(e.which == 13){
                $('form').submit();
            }
        });

        $('form').submit(function () {
            var message = $('.textarea').val();

            $.post('handlers/messages.php?action=sendMessage&message='+message, function (response) {
                if(response == 1){
                    LoadChat();
                    var Form = document.getElementById('messageFrm');
                    console.log(Form);
                    Form.reset();
                }
            });

            return false;
        });

        function profileClick(user){
            window.location = "personProfile.php?username="+user;
        }
    </script>
</body>
</html>