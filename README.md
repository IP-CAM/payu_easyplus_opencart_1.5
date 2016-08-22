<p align="center">
<img src="https://cloud.githubusercontent.com/assets/5717025/15674295/335fa446-273c-11e6-9db1-76c1b89d153d.jpg" alt="1">
</p>

# payu_easyplus_opencart_1.5
PayU's updated plugin for OpenCart v1.5.x.

These instructions are for a manual installation using FTP, cPanel or other web hosting Control Panel.

Before proceeding with the installation of the plugin, you must have installed OpenCart shopping cart.

This plugin was tested with OpenCart version v1.5.x only. It is NOT RECOMMENDED to be installed on OpenCart version v2.x.

Beyond having PHP5 SOAP extension enabled on your Web server there are no specific requirements for installing and configuring the plugin.

- Linux Install -

1. Upload all of the files and folders to your server from the "upload" folder, place them in your web root. The web root is different on some servers, cPanel it should be public_html/ and on Plesk it should be httpdocs/.
2. Visit the store admin e.g. http://www.example.com/admin or http://www.example.com/store/admin if OpenCart was installed in a subfolder of the web root.
3. Login with your admin credentials.
![image](https://cloud.githubusercontent.com/assets/5717025/17853946/d9283d88-686f-11e6-9005-85bcb638d65c.png)
4. Navigate to Extensions -> Payments, Find PayU secure payments and click “Install” to install the extension.
![image](https://cloud.githubusercontent.com/assets/5717025/17853956/eaab7700-686f-11e6-859f-9c3f6eb6baf1.png)
5. Click “Edit” to configure extension.
![image](https://cloud.githubusercontent.com/assets/5717025/17853972/ffaf8f24-686f-11e6-85fd-a1308f32629f.png)
6. The default values are used for testing purposes. Replace the default values with your credentials provided by PayU when you opened an account. This includes the safe key, API username, and API password. Select the Payment Methods activated on your PayU account. Only use debug or extended debug logging during testing. Also remember to Enable the plugin. Switch to live mode before accepting real transactions and select the desired payment currency. Finally click “Save” to save your changes.
![image](https://cloud.githubusercontent.com/assets/5717025/17853985/108557fc-6870-11e6-8d46-b5f37fcfda18.png)
7. PayU payment method should now be available as a payment method upon checkout.
![image](https://cloud.githubusercontent.com/assets/5717025/17853997/1c2a5c06-6870-11e6-846f-e110300d61cb.png)
8. Redirection to PayU to complete payment after a customer confirms order.
![image](https://cloud.githubusercontent.com/assets/5717025/17854015/2d46f274-6870-11e6-92d3-0e5da2307f17.png)

- Windows Install -
1. Upload all the files and folders to your server from the "upload" folder. This can be to anywhere of your choice. e.g. /wwwroot/store or /wwwroot
2. Follow the instructions of step 2 till the end of Linux install instructions.

