# CloudFlush
This Magento module has been designed to automatically clear the CloudFlare cache that your store sits behind when you flush the Magento cache via the "Cache Management" screen.

#### Compatibility
This extension has been tested with Magento 1.9.2.4 but is likely to work with older versions.

#### Installation
Copy and paste the folders into the root of your Magento installation. Test fully on a staging system before deploying to a live site.

On Mac merging the folders can be done by holding the Option (Alt) key while copy and pasting.
On Windows, I believe the file copy automatically offers to merge the files.

#### Configuration
1. Navigate to System > Configuration,
2. Select the CloudFlush option under Advanced,
3. Enter your CloudFlare email address and API key and click Save,
4. When the page reloads select which site you would like the cache flushed on,
5. When you are happy with the configuration set Status to Enable.

#### Usage
Navigate to System > Cache Management as normal and select "Flush Magento Cache". The Magento cache will flush as normal and a request will also be sent to CloudFlare to flush the cache. When the page it will indicate if the cache was flushed OK of if there was an error.

#### Troubleshooting
The extension logs problems to var/log/CloudFlush.log Check this first, it should give you an idea of what happened.

#### Help
Please feel free to email support@newbury.me if you need any help. I would love to hear any suggestions or bug reports so I can fix them in future releases.

#### License
This software has been released under the MIT License. See the LICENSE file for details.
