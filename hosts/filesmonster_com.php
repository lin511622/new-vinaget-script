<?php

class dl_filesmonster_com extends Download
{

    public function CheckAcc($cookie)
    {
        $data = $this->lib->curl("https://filesmonster.com/", "yab_ulang=en;" . $cookie, "");

        if (stristr($data, 'Your membership type: <strong>Premium')) {
            return array(true, "Until " . $this->lib->cut_str($data, "<p>Valid until: <span class='em-success'>", "</span></p>"));
        } elseif (stristr($data, "text-danger'>Premium expired:")) {
            return array(false, "Account Expired!");
        } elseif (stristr($data, 'Your membership type: <strong>Regular')) {
            return array(false, "accfree");
        } else {
            return array(false, "accinvalid");
        }

    }

    public function Login($user, $pass)
    {
        $data = $this->lib->curl("https://filesmonster.com/login.php", "yab_ulang=en", "act=login&user={$user}&pass={$pass}&captcha_shown=0&login=Login");
        $cookie = "yab_ulang=en;" . $this->lib->GetCookies($data);

        return array(true, $cookie);
    }

    public function Leech($url)
    {
        $data = $this->lib->curl($url, $this->lib->cookie, "");

        if (stristr($data, 'File not found') || stristr($data, '<h1 class="block_header">The link could not be decoded</h1>')) {
            $this->error("dead", true, false, 2);
        } elseif (stristr($data, 'Today you have already downloaded')) {
            $this->error("LimitAcc", true, false);
        } elseif (preg_match('/href="(https?:\/\/filesmonster\.com\/get\/.*?)" class="premium-button"/', $data, $data1)) {
            $data2 = $this->lib->curl($data1[1], $this->lib->cookie, "");
            if (preg_match('/get_link\("([^"\'><\r\n\t]+)"\)/', $data2, $data3)) {
                $data4 = $this->lib->curl("https://filesmonster.com" . $data3[1], $this->lib->cookie, "");
                if (preg_match('%url":"(https?:.+fmdepo.net.+)"%U', $data4, $giay)) {
                    $giay = str_replace('\\', '', $giay[1]);
                    $giay = str_replace("https", "http", $giay);
                    return trim($giay);
                }
            }
        }
        return false;
    }

}

/*
 * Open Source Project
 * New Vinaget by LTT
 * Version: 3.3 LTS
 * Filesmonster.com Download Plugin
 * Date: 01.09.2018
 */
