
<div class="frame-wrapper">
    <iframe id="childFrame" name="childFrame" frameborder="0" height="100%" width="100%"></iframe>
</div>

<!-- hidden input -->
<input type="hidden" id="siteUrl" name="siteUrl" value="<?php echo get_home_url(); ?>">
<input type="hidden" id="settingsHeadScript" value="<?php echo esc_html( wp_unslash( get_option( 'ce_insert_header' ))); ?>">
<input type="hidden" id="settingsBodyScript" value="<?php echo esc_html( wp_unslash( get_option( 'ce_insert_footer' ))); ?>">
<!-- end of hidden input -->



<!--javascript -->
<script>
let data = {};

var url = "<?php echo  get_home_url(); ?>"

var hashParam = window.location.search.match(/hash=([^&]*)/);
if (hashParam) {
    var hash = hashParam[1];
    document.getElementById('childFrame').src = "https://admin.zach.cloudengage.com/verify-signup/" + hash + '?site='+ url;
} else {
    document.getElementById('childFrame').src = "https://admin.zach.cloudengage.com/enable-wordpress?site=" + url;
}

// recieve iframe post message
window.addEventListener('message', () => {
    if (event.data.wpApiKey === "FkgFezruTQA8P6CS4vuVAPkPVLkMUZumvvHR27pN374Hzrm5WqMpjy2DGwR6vRCy") {
        if(/^https?:\/{2}.+\.?\w+\.cloudengage.com/i.test(event.origin)) {
            if (event.data.headScript || event.data.bodyScript) {
                var currentHeadScript = event.data.headScript;
                var currentBodyScript = event.data.bodyScript;
                var settingsHeadScript = document.getElementById('settingsHeadScript').value;
                var settingsBodyScript = document.getElementById('settingsBodyScript').value;

                if ((currentHeadScript != settingsHeadScript) || (currentBodyScript != settingsBodyScript)) {
                    post("options-general.php?page=cloudengage", event.data);
                } else {
                    let childFrame = document.getElementById('childFrame');
                    childFrame.contentWindow.postMessage('install', '*');
                }

            }
        }
    }
},false);

// create post and submit
function post(path, params, method) {
    method = method || "post";

    let form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(let key in params) {
        if(params.hasOwnProperty(key)) {
            let hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);
            form.appendChild(hiddenField);
         }

         // create nonce
         let nonceField = document.createElement("input");
         nonceField.setAttribute("type", "hidden");
         nonceField.setAttribute("id", "_wpnonce");
         nonceField.setAttribute("name", "_wpnonce");
         nonceField.setAttribute("value", "<?php echo wp_create_nonce('check_nonce'); ?>");
         form.appendChild(nonceField);
    }
    document.body.appendChild(form);
    form.submit();
}
</script>
<!-- end of javascript -->
