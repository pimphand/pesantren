<?php

/** Adminer - Compact database management
* @link https://www.adminer.org/
*
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*
* @version 5.0.6
*/

namespace Adminer;

$ia = '5.0.6';
error_reporting(6135);
set_error_handler(function ($yc, $_c) {
    return (bool) preg_match('~^(Trying to access array offset on( value of type)? null|Undefined (array key|property))~', $_c);
}, E_WARNING);
$Uc = ! preg_match('~^(unsafe_raw)?$~', ini_get('filter.default'));
if ($Uc || ini_get('filter.default_flags')) {
    foreach (['_GET', '_POST', '_COOKIE', '_SERVER'] as $X) {
        $Gi = filter_input_array(constant("INPUT$X"), FILTER_UNSAFE_RAW);
        if ($Gi) {
            $$X = $Gi;
        }
    }
}if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('8bit');
}function connection()
{
    global $g;

    return $g;
}function adminer()
{
    global $b;

    return $b;
}function driver()
{
    global $m;

    return $m;
}function version()
{
    global $ia;

    return $ia;
}function idf_unescape($w)
{
    if (! preg_match('~^[`\'"[]~', $w)) {
        return $w;
    }$ne = substr($w, -1);

    return str_replace($ne.$ne, $ne, substr($w, 1, -1));
}function q($Q)
{
    global $g;

    return $g->quote($Q);
}function escape_string($X)
{
    return substr(q($X), 1, -1);
}function number($X)
{
    return preg_replace('~[^0-9]+~', '', $X);
}function number_type()
{
    return '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';
}function remove_slashes($rg, $Uc = false)
{
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
        while ([$z, $X] = each($rg)) {
            foreach ($X as $ee => $W) {
                unset($rg[$z][$ee]);
                if (is_array($W)) {
                    $rg[$z][stripslashes($ee)] = $W;
                    $rg[] = &$rg[$z][stripslashes($ee)];
                } else {
                    $rg[$z][stripslashes($ee)] = ($Uc ? $W : stripslashes($W));
                }
            }
        }
    }
}function bracket_escape($w, $Ea = false)
{
    static $qi = [':' => ':1', ']' => ':2', '[' => ':3', '"' => ':4'];

    return strtr($w, ($Ea ? array_flip($qi) : $qi));
}function min_version($Xi, $Ae = '', $h = null)
{
    global $g;
    if (! $h) {
        $h = $g;
    }$jh = $h->server_info;
    if ($Ae && preg_match('~([\d.]+)-MariaDB~', $jh, $B)) {
        $jh = $B[1];
        $Xi = $Ae;
    }

return $Xi && version_compare($jh, $Xi) >= 0;
}function charset($g)
{
    return min_version('5.5.3', 0, $g) ? 'utf8mb4' : 'utf8';
}function ini_bool($Qd)
{
    $X = ini_get($Qd);

    return preg_match('~^(on|true|yes)$~i', $X) || (int) $X;
}function sid()
{
    static $J;
    if ($J === null) {
        $J = (SID && ! ($_COOKIE && ini_bool('session.use_cookies')));
    }

return $J;
}function set_password($Wi, $N, $V, $F)
{
    $_SESSION['pwds'][$Wi][$N][$V] = ($_COOKIE['adminer_key'] && is_string($F) ? [encrypt_string($F, $_COOKIE['adminer_key'])] : $F);
}function get_password()
{
    $J = get_session('pwds');
    if (is_array($J)) {
        $J = ($_COOKIE['adminer_key'] ? decrypt_string($J[0], $_COOKIE['adminer_key']) : false);
    }

return $J;
}function get_val($H, $o = 0)
{
    global $g;

    return $g->result($H, $o);
}function get_vals($H, $d = 0)
{
    global $g;
    $J = [];
    $I = $g->query($H);
    if (is_object($I)) {
        while ($K = $I->fetch_row()) {
            $J[] = $K[$d];
        }
    }

return $J;
}function get_key_vals($H, $h = null, $mh = true)
{
    global $g;
    if (! is_object($h)) {
        $h = $g;
    }$J = [];
    $I = $h->query($H);
    if (is_object($I)) {
        while ($K = $I->fetch_row()) {
            if ($mh) {
                $J[$K[0]] = $K[1];
            } else {
                $J[] = $K[0];
            }
        }
    }

return $J;
}function get_rows($H, $h = null, $n = "<p class='error'>")
{
    global $g;
    $rb = (is_object($h) ? $h : $g);
    $J = [];
    $I = $rb->query($H);
    if (is_object($I)) {
        while ($K = $I->fetch_assoc()) {
            $J[] = $K;
        }
    } elseif (! $I && ! is_object($h) && $n && (defined('Adminer\PAGE_HEADER') || $n == '-- ')) {
        echo $n.error()."\n";
    }

return $J;
}function unique_array($K, $y)
{
    foreach ($y as $x) {
        if (preg_match('~PRIMARY|UNIQUE~', $x['type'])) {
            $J = [];
            foreach ($x['columns'] as $z) {
                if (! isset($K[$z])) {
                    continue 2;
                }$J[$z] = $K[$z];
            }

return $J;
        }
    }
}function escape_key($z)
{
    if (preg_match('(^([\w(]+)('.str_replace('_', '.*', preg_quote(idf_escape('_'))).')([ \w)]+)$)', $z, $B)) {
        return $B[1].idf_escape(idf_unescape($B[2])).$B[3];
    }

return idf_escape($z);
}function where($Z, $p = [])
{
    global $g;
    $J = [];
    foreach ((array) $Z['where'] as $z => $X) {
        $z = bracket_escape($z, 1);
        $d = escape_key($z);
        $Sc = $p[$z]['type'];
        $J[] = $d.(JUSH == 'sql' && $Sc == 'json' ? ' = CAST('.q($X).' AS JSON)' : (JUSH == 'sql' && is_numeric($X) && preg_match('~\.~', $X) ? ' LIKE '.q($X) : (JUSH == 'mssql' && strpos($Sc, 'datetime') === false ? ' LIKE '.q(preg_replace('~[_%[]~', '[\0]', $X)) : ' = '.unconvert_field($p[$z], q($X)))));
        if (JUSH == 'sql' && preg_match('~char|text~', $Sc) && preg_match('~[^ -@]~', $X)) {
            $J[] = "$d = ".q($X).' COLLATE '.charset($g).'_bin';
        }
    }foreach ((array) $Z['null'] as $z) {
        $J[] = escape_key($z).' IS NULL';
    }

return implode(' AND ', $J);
}function where_check($X, $p = [])
{
    parse_str($X, $Va);
    remove_slashes([&$Va]);

    return where($Va, $p);
}function where_link($u, $d, $Y, $uf = '=')
{
    return "&where%5B$u%5D%5Bcol%5D=".urlencode($d)."&where%5B$u%5D%5Bop%5D=".urlencode(($Y !== null ? $uf : 'IS NULL'))."&where%5B$u%5D%5Bval%5D=".urlencode($Y);
}function convert_fields($e, $p, $M = [])
{
    $J = '';
    foreach ($e as $z => $X) {
        if ($M && ! in_array(idf_escape($z), $M)) {
            continue;
        }$ya = convert_field($p[$z]);
        if ($ya) {
            $J .= ", $ya AS ".idf_escape($z);
        }
    }

return $J;
}function cookie($C, $Y, $ve = 2592000)
{
    global $ba;

    return header("Set-Cookie: $C=".urlencode($Y).($ve ? '; expires='.gmdate('D, d M Y H:i:s', time() + $ve).' GMT' : '').'; path='.preg_replace('~\?.*~', '', $_SERVER['REQUEST_URI']).($ba ? '; secure' : '').'; HttpOnly; SameSite=lax', false);
}function get_settings($zb)
{
    parse_str($_COOKIE[$zb], $nh);

    return $nh;
}function get_setting($z, $zb = 'adminer_settings')
{
    $nh = get_settings($zb);

    return $nh[$z];
}function save_settings($nh, $zb = 'adminer_settings')
{
    return cookie($zb, http_build_query($nh + get_settings($zb)));
}function restart_session()
{
    if (! ini_bool('session.use_cookies')) {
        session_start();
    }
}function stop_session($bd = false)
{
    $Oi = ini_bool('session.use_cookies');
    if (! $Oi || $bd) {
        session_write_close();
        if ($Oi && @ini_set('session.use_cookies', false) === false) {
            session_start();
        }
    }
}function &get_session($z)
{
    return $_SESSION[$z][DRIVER][SERVER][$_GET['username']];
}function set_session($z, $X)
{
    $_SESSION[$z][DRIVER][SERVER][$_GET['username']] = $X;
}function auth_url($Wi, $N, $V, $k = null)
{
    global $bc;
    preg_match('~([^?]*)\??(.*)~', remove_from_uri(implode('|', array_keys($bc)).'|username|'.($k !== null ? 'db|' : '').session_name()), $B);

    return "$B[1]?".(sid() ? SID.'&' : '').($Wi != 'server' || $N != '' ? urlencode($Wi).'='.urlencode($N).'&' : '').'username='.urlencode($V).($k != '' ? '&db='.urlencode($k) : '').($B[2] ? "&$B[2]" : '');
}function is_ajax()
{
    return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}function redirect($xe, $Me = null)
{
    if ($Me !== null) {
        restart_session();
        $_SESSION['messages'][preg_replace('~^[^?]*~', '', ($xe !== null ? $xe : $_SERVER['REQUEST_URI']))][] = $Me;
    }if ($xe !== null) {
        if ($xe == '') {
            $xe = '.';
        }header("Location: $xe");
        exit;
    }
}function query_redirect($H, $xe, $Me, $_g = true, $Ec = true, $Nc = false, $di = '')
{
    global $g,$n,$b;
    if ($Ec) {
        $Ch = microtime(true);
        $Nc = ! $g->query($H);
        $di = format_time($Ch);
    }$xh = '';
    if ($H) {
        $xh = $b->messageQuery($H, $di, $Nc);
    }if ($Nc) {
        $n = error().$xh.script('messagesPrint();');

        return false;
    }if ($_g) {
        redirect($xe, $Me.$xh);
    }

return true;
}function queries($H)
{
    global $g;
    static $vg = [];
    static $Ch;
    if (! $Ch) {
        $Ch = microtime(true);
    }if ($H === null) {
        return [implode("\n", $vg), format_time($Ch)];
    }$vg[] = (preg_match('~;$~', $H) ? "DELIMITER ;;\n$H;\nDELIMITER " : $H).';';

    return $g->query($H);
}function apply_queries($H, $T, $Ac = 'Adminer\table')
{
    foreach ($T as $R) {
        if (! queries("$H ".$Ac($R))) {
            return false;
        }
    }

return true;
}function queries_redirect($xe, $Me, $_g)
{
    [$vg, $di] = queries(null);

    return query_redirect($vg, $xe, $Me, $_g, false, ! $_g, $di);
}function format_time($Ch)
{
    return lang(0, max(0, microtime(true) - $Ch));
}function relative_uri()
{
    return str_replace(':', '%3a', preg_replace('~^[^?]*/([^?]*)~', '\1', $_SERVER['REQUEST_URI']));
}function remove_from_uri($Qf = '')
{
    return substr(preg_replace("~(?<=[?&])($Qf".(SID ? '' : '|'.session_name()).')=[^&]*&~', '', relative_uri().'&'), 0, -1);
}function get_file($z, $Ob = false, $Sb = '')
{
    $Tc = $_FILES[$z];
    if (! $Tc) {
        return null;
    }foreach ($Tc as $z => $X) {
        $Tc[$z] = (array) $X;
    }$J = '';
    foreach ($Tc['error'] as $z => $n) {
        if ($n) {
            return $n;
        }$C = $Tc['name'][$z];
        $li = $Tc['tmp_name'][$z];
        $vb = file_get_contents($Ob && preg_match('~\.gz$~', $C) ? "compress.zlib://$li" : $li);
        if ($Ob) {
            $Ch = substr($vb, 0, 3);
            if (function_exists('iconv') && preg_match("~^\xFE\xFF|^\xFF\xFE~", $Ch)) {
                $vb = iconv('utf-16', 'utf-8', $vb);
            } elseif ($Ch == "\xEF\xBB\xBF") {
                $vb = substr($vb, 3);
            }
        }$J .= $vb;
        if ($Sb) {
            $J .= (preg_match("($Sb\\s*\$)", $vb) ? '' : $Sb)."\n\n";
        }
    }

return $J;
}function upload_error($n)
{
    $Ie = ($n == UPLOAD_ERR_INI_SIZE ? ini_get('upload_max_filesize') : 0);

    return $n ? lang(1).($Ie ? ' '.lang(2, $Ie) : '') : lang(3);
}function repeat_pattern($ag, $te)
{
    return str_repeat("$ag{0,65535}", $te / 65535)."$ag{0,".($te % 65535).'}';
}function is_utf8($X)
{
    return preg_match('~~u', $X) && ! preg_match('~[\0-\x8\xB\xC\xE-\x1F]~', $X);
}function shorten_utf8($Q, $te = 80, $Ih = '')
{
    if (! preg_match('(^('.repeat_pattern("[\t\r\n -\x{10FFFF}]", $te).')($)?)u', $Q, $B)) {
        preg_match('(^('.repeat_pattern("[\t\r\n -~]", $te).')($)?)', $Q, $B);
    }

return h($B[1]).$Ih.(isset($B[2]) ? '' : '<i>…</i>');
}function format_number($X)
{
    return strtr(number_format($X, 0, '.', lang(4)), preg_split('~~u', lang(5), -1, PREG_SPLIT_NO_EMPTY));
}function friendly_url($X)
{
    return preg_replace('~\W~i', '-', $X);
}function table_status1($R, $Oc = false)
{
    $J = table_status($R, $Oc);

    return $J ?: ['Name' => $R];
}function column_foreign_keys($R)
{
    global $b;
    $J = [];
    foreach ($b->foreignKeys($R) as $r) {
        foreach ($r['source'] as $X) {
            $J[$X][] = $r;
        }
    }

return $J;
}function fields_from_edit()
{
    global $m;
    $J = [];
    foreach ((array) $_POST['field_keys'] as $z => $X) {
        if ($X != '') {
            $X = bracket_escape($X);
            $_POST['function'][$X] = $_POST['field_funs'][$z];
            $_POST['fields'][$X] = $_POST['field_vals'][$z];
        }
    }foreach ((array) $_POST['fields'] as $z => $X) {
        $C = bracket_escape($z, 1);
        $J[$C] = ['field' => $C, 'privileges' => ['insert' => 1, 'update' => 1, 'where' => 1, 'order' => 1], 'null' => 1, 'auto_increment' => ($z == $m->primary)];
    }

return $J;
}function dump_headers($Ed, $Ue = false)
{
    global $b;
    $J = $b->dumpHeaders($Ed, $Ue);
    $Mf = $_POST['output'];
    if ($Mf != 'text') {
        header('Content-Disposition: attachment; filename='.$b->dumpFilename($Ed).".$J".($Mf != 'file' && preg_match('~^[0-9a-z]+$~', $Mf) ? ".$Mf" : ''));
    }session_write_close();
    ob_flush();
    flush();

    return $J;
}function dump_csv($K)
{
    foreach ($K as $z => $X) {
        if (preg_match('~["\n,;\t]|^0|\.\d*0$~', $X) || $X === '') {
            $K[$z] = '"'.str_replace('"', '""', $X).'"';
        }
    }echo implode(($_POST['format'] == 'csv' ? ',' : ($_POST['format'] == 'tsv' ? "\t" : ';')), $K)."\r\n";
}function apply_sql_function($t, $d)
{
    return $t ? ($t == 'unixepoch' ? "DATETIME($d, '$t')" : ($t == 'count distinct' ? 'COUNT(DISTINCT ' : strtoupper("$t("))."$d)") : $d;
}function get_temp_dir()
{
    $J = ini_get('upload_tmp_dir');
    if (! $J) {
        if (function_exists('sys_get_temp_dir')) {
            $J = sys_get_temp_dir();
        } else {
            $q = @tempnam('', '');
            if (! $q) {
                return false;
            }$J = dirname($q);
            unlink($q);
        }
    }

return $J;
}function file_open_lock($q)
{
    if (is_link($q)) {
        return;
    }$s = @fopen($q, 'c+');
    if (! $s) {
        return;
    }chmod($q, 0660);
    if (! flock($s, LOCK_EX)) {
        fclose($s);

        return;
    }

return $s;
}function file_write_unlock($s, $Ib)
{
    rewind($s);
    fwrite($s, $Ib);
    ftruncate($s, strlen($Ib));
    file_unlock($s);
}function file_unlock($s)
{
    flock($s, LOCK_UN);
    fclose($s);
}function password_file($i)
{
    $q = get_temp_dir().'/adminer.key';
    if (! $i && ! file_exists($q)) {
        return false;
    }$s = file_open_lock($q);
    if (! $s) {
        return false;
    }$J = stream_get_contents($s);
    if (! $J) {
        $J = rand_string();
        file_write_unlock($s, $J);
    } else {
        file_unlock($s);
    }

return $J;
}function rand_string()
{
    return md5(uniqid(mt_rand(), true));
}function select_value($X, $A, $o, $ci)
{
    global $b;
    if (is_array($X)) {
        $J = '';
        foreach ($X as $ee => $W) {
            $J .= '<tr>'.($X != array_values($X) ? '<th>'.h($ee) : '').'<td>'.select_value($W, $A, $o, $ci);
        }

return "<table>$J</table>";
    }if (! $A) {
        $A = $b->selectLink($X, $o);
    }if ($A === null) {
        if (is_mail($X)) {
            $A = "mailto:$X";
        }if (is_url($X)) {
            $A = $X;
        }
    }$J = $b->editVal($X, $o);
    if ($J !== null) {
        if (! is_utf8($J)) {
            $J = "\0";
        } elseif ($ci != '' && is_shortable($o)) {
            $J = shorten_utf8($J, max(0, +$ci));
        } else {
            $J = h($J);
        }
    }

return $b->selectVal($J, $A, $o, $X);
}function is_mail($oc)
{
    $za = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';
    $ac = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
    $ag = "$za+(\\.$za+)*@($ac?\\.)+$ac";

    return is_string($oc) && preg_match("(^$ag(,\\s*$ag)*\$)i", $oc);
}function is_url($Q)
{
    $ac = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';

    return preg_match("~^(https?)://($ac?\\.)+$ac(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i", $Q);
}function is_shortable($o)
{
    return preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~', $o['type']);
}function count_rows($R, $Z, $Yd, $pd)
{
    $H = ' FROM '.table($R).($Z ? ' WHERE '.implode(' AND ', $Z) : '');

    return $Yd && (JUSH == 'sql' || count($pd) == 1) ? 'SELECT COUNT(DISTINCT '.implode(', ', $pd).")$H" : 'SELECT COUNT(*)'.($Yd ? " FROM (SELECT 1$H GROUP BY ".implode(', ', $pd).') x' : $H);
}function slow_query($H)
{
    global $b,$mi,$m;
    $k = $b->database();
    $ei = $b->queryTimeout();
    $rh = $m->slowQuery($H, $ei);
    $h = null;
    if (! $rh && support('kill') && is_object($h = connect($b->credentials())) && ($k == '' || $h->select_db($k))) {
        $he = $h->result(connection_id());
        echo script("var timeout = setTimeout(function () { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$he&token=$mi'); }, 1000 * $ei);");
    }ob_flush();
    flush();
    $J = @get_key_vals(($rh ?: $H), $h, false);
    if ($h) {
        echo script('clearTimeout(timeout);');
        ob_flush();
        flush();
    }

return $J;
}function get_token()
{
    $yg = rand(1, 1e6);

    return ($yg ^ $_SESSION['token']).":$yg";
}function verify_token()
{
    [$mi, $yg] = explode(':', $_POST['token']);

    return ($yg ^ $_SESSION['token']) == $mi;
}function lzw_decompress($Ja)
{
    $Wb = 256;
    $Ka = 8;
    $eb = [];
    $Jg = 0;
    $Kg = 0;
    for ($u = 0; $u < strlen($Ja); $u++) {
        $Jg = ($Jg << 8) + ord($Ja[$u]);
        $Kg += 8;
        if ($Kg >= $Ka) {
            $Kg -= $Ka;
            $eb[] = $Jg >> $Kg;
            $Jg &= (1 << $Kg) - 1;
            $Wb++;
            if ($Wb >> $Ka) {
                $Ka++;
            }
        }
    }$Vb = range("\0", "\xFF");
    $J = '';
    foreach ($eb as $u => $db) {
        $nc = $Vb[$db];
        if (! isset($nc)) {
            $nc = $gj.$gj[0];
        }$J .= $nc;
        if ($u) {
            $Vb[] = $gj.$nc[0];
        }$gj = $nc;
    }

return $J;
}function script($uh, $pi = "\n")
{
    return '<script'.nonce().">$uh</script>$pi";
}function script_src($Li)
{
    return "<script src='".h($Li)."'".nonce()."></script>\n";
}function nonce()
{
    return ' nonce="'.get_nonce().'"';
}function target_blank()
{
    return ' target="_blank" rel="noreferrer noopener"';
}function h($Q)
{
    return str_replace("\0", '&#0;', htmlspecialchars($Q, ENT_QUOTES, 'utf-8'));
}function nl_br($Q)
{
    return str_replace("\n", '<br>', $Q);
}function checkbox($C, $Y, $Ya, $je = '', $tf = '', $cb = '', $ke = '')
{
    $J = "<input type='checkbox' name='$C' value='".h($Y)."'".($Ya ? ' checked' : '').($ke ? " aria-labelledby='$ke'" : '').'>'.($tf ? script("qsl('input').onclick = function () { $tf };", '') : '');

    return $je != '' || $cb ? '<label'.($cb ? " class='$cb'" : '').">$J".h($je).'</label>' : $J;
}function optionlist($yf, $bh = null, $Pi = false)
{
    $J = '';
    foreach ($yf as $ee => $W) {
        $zf = [$ee => $W];
        if (is_array($W)) {
            $J .= '<optgroup label="'.h($ee).'">';
            $zf = $W;
        }foreach ($zf as $z => $X) {
            $J .= '<option'.($Pi || is_string($z) ? ' value="'.h($z).'"' : '').($bh !== null && ($Pi || is_string($z) ? (string) $z : $X) === $bh ? ' selected' : '').'>'.h($X);
        }if (is_array($W)) {
            $J .= '</optgroup>';
        }
    }

return $J;
}function html_select($C, $yf, $Y = '', $sf = '', $ke = '')
{
    return "<select name='".h($C)."'".($ke ? " aria-labelledby='$ke'" : '').'>'.optionlist($yf, $Y).'</select>'.($sf ? script("qsl('select').onchange = function () { $sf };", '') : '');
}function html_radios($C, $yf, $Y = '')
{
    $J = '';
    foreach ($yf as $z => $X) {
        $J .= "<label><input type='radio' name='".h($C)."' value='".h($z)."'".($z == $Y ? ' checked' : '').'>'.h($X).'</label>';
    }

return $J;
}function confirm($Me = '', $ch = "qsl('input')")
{
    return script("$ch.onclick = function () { return confirm('".($Me ? js_escape($Me) : lang(6))."'); };", '');
}function print_fieldset($v, $se, $aj = false)
{
    echo '<fieldset><legend>',"<a href='#fieldset-$v'>$se</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$v');", ''),'</legend>',"<div id='fieldset-$v'".($aj ? '' : " class='hidden'").">\n";
}function bold($Ma, $cb = '')
{
    return $Ma ? " class='active $cb'" : ($cb ? " class='$cb'" : '');
}function js_escape($Q)
{
    return addcslashes($Q, "\r\n'\\/");
}function pagination($E, $Fb)
{
    return ' '.($E == $Fb ? $E + 1 : '<a href="'.h(remove_from_uri('page').($E ? "&page=$E".($_GET['next'] ? '&next='.urlencode($_GET['next']) : '') : '')).'">'.($E + 1).'</a>');
}function hidden_fields($rg, $Hd = [], $kg = '')
{
    $J = false;
    foreach ($rg as $z => $X) {
        if (! in_array($z, $Hd)) {
            if (is_array($X)) {
                hidden_fields($X, [], $z);
            } else {
                $J = true;
                echo '<input type="hidden" name="'.h($kg ? $kg."[$z]" : $z).'" value="'.h($X).'">';
            }
        }
    }

return $J;
}function hidden_fields_get()
{
    echo (sid() ? '<input type="hidden" name="'.session_name().'" value="'.h(session_id()).'">' : ''),(SERVER !== null ? '<input type="hidden" name="'.DRIVER.'" value="'.h(SERVER).'">' : ''),'<input type="hidden" name="username" value="'.h($_GET['username']).'">';
}function enum_input($U, $_a, $o, $Y, $rc = null)
{
    global $b;
    preg_match_all("~'((?:[^']|'')*)'~", $o['length'], $De);
    $J = ($rc !== null ? "<label><input type='$U'$_a value='$rc'".((is_array($Y) ? in_array($rc, $Y) : $Y === $rc) ? ' checked' : '').'><i>'.lang(7).'</i></label>' : '');
    foreach ($De[1] as $u => $X) {
        $X = stripcslashes(str_replace("''", "'", $X));
        $Ya = (is_array($Y) ? in_array($X, $Y) : $Y === $X);
        $J .= " <label><input type='$U'$_a value='".h($X)."'".($Ya ? ' checked' : '').'>'.h($b->editVal($X, $o)).'</label>';
    }

return $J;
}function input($o, $Y, $t, $Da = false)
{
    global $m,$b;
    $C = h(bracket_escape($o['field']));
    echo "<td class='function'>";
    if (is_array($Y) && ! $t) {
        $Y = json_encode($Y, 128);
        $t = 'json';
    }$Ig = (JUSH == 'mssql' && $o['auto_increment']);
    if ($Ig && ! $_POST['save']) {
        $t = null;
    }$kd = (isset($_GET['select']) || $Ig ? ['orig' => lang(8)] : []) + $b->editFunctions($o);
    $Xb = stripos($o['default'], 'GENERATED ALWAYS AS ') === 0 ? " disabled=''" : '';
    $_a = " name='fields[$C]'$Xb".($Da ? ' autofocus' : '');
    $xc = $m->enumLength($o);
    if ($xc) {
        $o['type'] = 'enum';
        $o['length'] = $xc;
    }echo $m->unconvertFunction($o).' ';
    if ($o['type'] == 'enum') {
        echo h($kd['']).'<td>'.$b->editInput($_GET['edit'], $o, $_a, $Y);
    } else {
        $wd = (in_array($t, $kd) || isset($kd[$t]));
        echo (count($kd) > 1 ? "<select name='function[$C]'$Xb>".optionlist($kd, $t === null || $wd ? $t : '').'</select>'.on_help("getTarget(event).value.replace(/^SQL\$/, '')", 1).script("qsl('select').onchange = functionChange;", '') : h(reset($kd))).'<td>';
        $Sd = $b->editInput($_GET['edit'], $o, $_a, $Y);
        if ($Sd != '') {
            echo $Sd;
        } elseif (preg_match('~bool~', $o['type'])) {
            echo "<input type='hidden'$_a value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i', $Y) ? " checked='checked'" : '')."$_a value='1'>";
        } elseif ($o['type'] == 'set') {
            preg_match_all("~'((?:[^']|'')*)'~", $o['length'], $De);
            foreach ($De[1] as $u => $X) {
                $X = stripcslashes(str_replace("''", "'", $X));
                $Ya = in_array($X, explode(',', $Y), true);
                echo " <label><input type='checkbox' name='fields[$C][$u]' value='".h($X)."'".($Ya ? ' checked' : '').'>'.h($b->editVal($X, $o)).'</label>';
            }
        } elseif (preg_match('~blob|bytea|raw|file~', $o['type']) && ini_bool('file_uploads')) {
            echo "<input type='file' name='fields-$C'>";
        } elseif (($ai = preg_match('~text|lob|memo~i', $o['type'])) || preg_match("~\n~", $Y)) {
            if ($ai && JUSH != 'sqlite') {
                $_a .= " cols='50' rows='12'";
            } else {
                $L = min(12, substr_count($Y, "\n") + 1);
                $_a .= " cols='30' rows='$L'".($L == 1 ? " style='height: 1.2em;'" : '');
            }echo "<textarea$_a>".h($Y).'</textarea>';
        } elseif ($t == 'json' || preg_match('~^jsonb?$~', $o['type'])) {
            echo "<textarea$_a cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';
        } else {
            $Ai = $m->types();
            $Ke = (! preg_match('~int~', $o['type']) && preg_match('~^(\d+)(,(\d+))?$~', $o['length'], $B) ? ((preg_match('~binary~', $o['type']) ? 2 : 1) * $B[1] + ($B[3] ? 1 : 0) + ($B[2] && ! $o['unsigned'] ? 1 : 0)) : ($Ai[$o['type']] ? $Ai[$o['type']] + ($o['unsigned'] ? 0 : 1) : 0));
            if (JUSH == 'sql' && min_version(5.6) && preg_match('~time~', $o['type'])) {
                $Ke += 7;
            }echo '<input'.((! $wd || $t === '') && preg_match('~(?<!o)int(?!er)~', $o['type']) && ! preg_match('~\[\]~', $o['full_type']) ? " type='number'" : '')." value='".h($Y)."'".($Ke ? " data-maxlength='$Ke'" : '').(preg_match('~char|binary~', $o['type']) && $Ke > 20 ? " size='40'" : '')."$_a>";
        }echo $b->editHint($_GET['edit'], $o, $Y);
        $Vc = 0;
        foreach ($kd as $z => $X) {
            if ($z === '' || ! $X) {
                break;
            }$Vc++;
        }if ($Vc) {
            echo script("mixin(qsl('td'), {onchange: partial(skipOriginal, $Vc), oninput: function () { this.onchange(); }});");
        }
    }
}function process_input($o)
{
    global $b,$m;
    if (stripos($o['default'], 'GENERATED ALWAYS AS ') === 0) {
        return null;
    }$w = bracket_escape($o['field']);
    $t = $_POST['function'][$w];
    $Y = $_POST['fields'][$w];
    if ($o['type'] == 'enum' || $m->enumLength($o)) {
        if ($Y == -1) {
            return false;
        }if ($Y == '') {
            return 'NULL';
        }
    }if ($o['auto_increment'] && $Y == '') {
        return null;
    }if ($t == 'orig') {
        return preg_match('~^CURRENT_TIMESTAMP~i', $o['on_update']) ? idf_escape($o['field']) : false;
    }if ($t == 'NULL') {
        return 'NULL';
    }if ($o['type'] == 'set') {
        $Y = implode(',', (array) $Y);
    }if ($t == 'json') {
        $t = '';
        $Y = json_decode($Y, true);
        if (! is_array($Y)) {
            return false;
        }

return $Y;
    }if (preg_match('~blob|bytea|raw|file~', $o['type']) && ini_bool('file_uploads')) {
        $Tc = get_file("fields-$w");
        if (! is_string($Tc)) {
            return false;
        }

return $m->quoteBinary($Tc);
    }

return $b->processInput($o, $Y, $t);
}function search_tables()
{
    global $b,$g;
    $_GET['where'][0]['val'] = $_POST['query'];
    $eh = "<ul>\n";
    foreach (table_status('', true) as $R => $S) {
        $C = $b->tableName($S);
        if (isset($S['Engine']) && $C != '' && (! $_POST['tables'] || in_array($R, $_POST['tables']))) {
            $I = $g->query('SELECT'.limit('1 FROM '.table($R), ' WHERE '.implode(' AND ', $b->selectSearchProcess(fields($R), [])), 1));
            if (! $I || $I->fetch_row()) {
                $ng = "<a href='".h(ME.'select='.urlencode($R).'&where[0][op]='.urlencode($_GET['where'][0]['op']).'&where[0][val]='.urlencode($_GET['where'][0]['val']))."'>$C</a>";
                echo "$eh<li>".($I ? $ng : "<p class='error'>$ng: ".error())."\n";
                $eh = '';
            }
        }
    }echo ($eh ? "<p class='message'>".lang(9) : '</ul>')."\n";
}function on_help($lb, $ph = 0)
{
    return script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $lb, $ph) }, onmouseout: helpMouseout});", '');
}function edit_form($R, $p, $K, $Ji)
{
    global $b,$mi,$n;
    $Oh = $b->tableName(table_status1($R, true));
    page_header(($Ji ? lang(10) : lang(11)), $n, ['select' => [$R, $Oh]], $Oh);
    $b->editRowPrint($R, $p, $K, $Ji);
    if ($K === false) {
        echo "<p class='error'>".lang(12)."\n";

        return;
    }echo "<form action='' method='post' enctype='multipart/form-data' id='form'>\n";
    if (! $p) {
        echo "<p class='error'>".lang(13)."\n";
    } else {
        echo "<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");
        $Da = ! $_POST;
        foreach ($p as $C => $o) {
            echo '<tr><th>'.$b->fieldName($o);
            $l = $_GET['set'][bracket_escape($C)];
            if ($l === null) {
                $l = $o['default'];
                if ($o['type'] == 'bit' && preg_match("~^b'([01]*)'\$~", $l, $Fg)) {
                    $l = $Fg[1];
                }if (JUSH == 'sql' && preg_match('~binary~', $o['type'])) {
                    $l = bin2hex($l);
                }
            }$Y = ($K !== null ? ($K[$C] != '' && JUSH == 'sql' && preg_match('~enum|set~', $o['type']) && is_array($K[$C]) ? implode(',', $K[$C]) : (is_bool($K[$C]) ? +$K[$C] : $K[$C])) : (! $Ji && $o['auto_increment'] ? '' : (isset($_GET['select']) ? false : $l)));
            if (! $_POST['save'] && is_string($Y)) {
                $Y = $b->editVal($Y, $o);
            }$t = ($_POST['save'] ? (string) $_POST['function'][$C] : ($Ji && preg_match('~^CURRENT_TIMESTAMP~i', $o['on_update']) ? 'now' : ($Y === false ? null : ($Y !== null ? '' : 'NULL'))));
            if (! $_POST && ! $Ji && $Y == $o['default'] && preg_match('~^[\w.]+\(~', $Y)) {
                $t = 'SQL';
            }if (preg_match('~time~', $o['type']) && preg_match('~^CURRENT_TIMESTAMP~i', $Y)) {
                $Y = '';
                $t = 'now';
            }if ($o['type'] == 'uuid' && $Y == 'uuid()') {
                $Y = '';
                $t = 'uuid';
            }if ($Da !== false) {
                $Da = ($o['auto_increment'] || $t == 'now' || $t == 'uuid' ? null : true);
            }input($o, $Y, $t, $Da);
            if ($Da) {
                $Da = false;
            }echo "\n";
        }if (! support('table')) {
            echo '<tr>'."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select('field_funs[]', $b->editFunctions(['null' => isset($_GET['select'])]))."<td><input name='field_vals[]'>"."\n";
        }echo "</table>\n";
    }echo "<p>\n";
    if ($p) {
        echo "<input type='submit' value='".lang(14)."'>\n";
        if (! isset($_GET['select'])) {
            echo "<input type='submit' name='insert' value='".($Ji ? lang(15) : lang(16))."' title='Ctrl+Shift+Enter'>\n",($Ji ? script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".lang(17)."…', this); };") : '');
        }
    }echo $Ji ? "<input type='submit' name='delete' value='".lang(18)."'>".confirm()."\n" : '';
    if (isset($_GET['select'])) {
        hidden_fields(['check' => (array) $_POST['check'], 'clone' => $_POST['clone'], 'all' => $_POST['all']]);
    }echo '<input type="hidden" name="referer" value="',h(isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER']),'">
<input type="hidden" name="save" value="1">
<input type="hidden" name="token" value="',$mi,'">
</form>
';
}if (isset($_GET['file'])) {
    if (substr($ia, -4) != '-dev') {
        if ($_SERVER['HTTP_IF_MODIFIED_SINCE']) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }header('Expires: '.gmdate('D, d M Y H:i:s', time() + 365 * 24 * 60 * 60).' GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: immutable');
    }if ($_GET['file'] == 'favicon.ico') {
        header('Content-Type: image/x-icon');
        echo lzw_decompress("\0\0\0` \0�\0\n @\0�C��\"\0`E�Q����?�tvM'�Jd�d\\�b0\0�\"��fӈ��s5����A�XPaJ�0���8�#R�T��z`�#.��c�X��Ȁ?�-\0�Im?�.�M��\0ȯ(̉��/(%�\0");
    } elseif ($_GET['file'] == 'default.css') {
        header('Content-Type: text/css; charset=utf-8');
        echo lzw_decompress("b7�'���o9�c`��a1���#y��d��C�1��tFQx�\\2�\n�S���n0�'#I��,\$M�c)��c����1i�Xi3ͦ���n)T�i��d:FcI�[��c��	��Fé�vt2�+�C,�a�G�F����:;Nu�)����Ǜ!�tl���F�|��,�`pw�S-����������oQk�� n�E��O+,=�4�mM���Ƌ�GS��Zh�6��. uO�M�C@����M'�(�b5�ҩ��H�a2)�qиpe6�?t#Z-���ox�<���s���;��H�4\$�䥍�ۚ��a�4�\"�(�!C,D�N��;����Jj����@�@�!����K�����6��jX�\r����@ 2@�b��(Z�Apl��8��h�.�=*H�4q3�AЂ�.��K���!�f�qr�!�1�Ȏ�c���*+ �(�\n�2�j���(dYA���D�t�ϑ�m*H�9+�0�0�\n0t���J�,E�ER� X��u[&@0�A��7=�;����K��;0�D�7Ajm*`�3:v`��ūk��Ʊ.�x�Xv(ec�������Emz\\�0C�G2��Jt2(à��c�N<��s^�2��6Z̅�?c��X�m���ϥ(�d9?>�/�Y^I�%����5=H==\0T �6���\"��ح�\r���mH��-C��z��\n����Q�j<<Z��6��v>����~LHť��p�,�Yp��P�9{}�g߻5���Y�	>g5�@8o�\n>��-nҩ��ԐR)J��#j<7�(��ĸN%�}��*2��\rA�јF0�Xވ�Cp�:��Z�Ƭ��:G�ԏ�v=�j�[C\r��z/��:@���B��<�(z.�P���Y�h7\"�j�/܉��]�\\���6`�Ҋ4=x�^1��\0C0����q�!�4�%l�SP[K}�v�#��@g�����\r�h=�����RGa���TTRNv��:�\"���u���\\e�4	U\rt�W�����Z �CF,@����1\r�\r�0��p!�OY4:����!�����hV��\n�`	�pn�Ϛ\0Y+�20Z�anYq5�p��\"a|��p��n#�U\0�8H���bIp(��s��Yt���HI&�zN0ؖ@���tAf��)��ƙZFՂlKr�/C`�C�B�1*�&��th���WI�0C�\\*)mJ���[âdi3K\0�)�dϘJ261��\n-o�٫��0ߤ���X�b\r�qp��ԆOY0X��֟�C`�S�a��Z�[�t#�x��@2\r��Z̲�� �b-��]C\\��l� \"5,���R����j�t�K�P`��,���s�\r�1xО@���K\"d�'��F.6,T	�[�M�֦��X������������Bx�2�j�иɃC��(�Mm�d�\"��W�t����8�sDY��Z �\"�VS暙�D2��+%�?�VD�#�����9Gr���S�b�e�,\r�S���k�	����(��]a��\r��1�P䀛iɓu\$���	��sbA��>T�T�n�e7�n�R<xf��'��C��5���^���)�\n������`-\"g���Qy�\r�ת�`BN\nAd�#@��)\$�R}�@����yte&r��@����+���[�T�\nX��셜��vAG��U.�]j�`��r*���x����X��Qp-��B��Cy�r���<�.\0#��:�K��)l�NH���x��WP���������8���<��s��6��Ȯʅwʒ���ypc�E��J��w�Q�A�+�4/�02d��mÐf ��\$��ţ4vvy�`I�\$�d�5U\0�Pj(%�K���47�La��^��zM\0�x�m���х���RTy����!���w�m�j[F��{�ca�����m�V� <��s,�������9T�]gu���H�/���V+y�jM���ݨM�G���;�%���mW\r�ђ8��0�ϴ6�K�����W�{�k�]Z�]ÈO*(`#t������A�6�{��%Oaq�H���Er�UC(b\ret�%I��X�\\�0��X�YQ	��(T-���2H���r�����E�qd�w��AKY�W ܹ��<��1�Gp��z��\$�h6X��*f�\"<:��;V���6����;@���@�w��r���K�ѽ�@o��<�\0}v�����M���=���<�GD�wᷴY���pT�s�?~��=��=��Fn?x�������=�w���_(7����R'��Y�| \\y�z�n���}��=%p�0j?���B!���\nT��o��l����k���GHu`4J�/G�]@�	~=N�&�H%��jK|\0pj�8����Np��xׯ��K�5+��*��L��b��\$kI\rv�휎����P��z�~Q0��o�=�\r�i��Y�կ���������Д�\nm�ް�p��`�p:&�&��۰�����H�p��	-��P�\n�p��U\n`�I6��&d�i<�)x�h�s����4�pJ��q4@��7\0Z\0�HJ�g˥�F�����5���N*��!�r�¥@������a®]�`EH�cG@�&���fF��Zː�H�v	��4/\0�&�ąqH.�H�?b-��a7@Ƙ1�i����S�曑�qQKb�E\0�RŃ�8�Q �4Z���Q.8�����\n\r��!�(?�>s�K#gQ\$r0/�P8��\"rW��#�Ds�1%�U&Ì��'2@�y%�P�kc'Re&2Z6\0�)Rꒋ�\"%&Q:�O��R��\"ҍ%�\r�c,2���'�a)�f��&%Q,R� ��-�c �	.hI.���.2�-�\r*\"�*qlX����I%��!����.�R+1o)���!-S0)��\r�6��`��R��2�Y)��Y,�3�552�)� �SD�IDB�/4�f~��D��0R#1�1�8�\"��4��28�2�?S��R�,Ӟ��5RI:��(ө��;s� �1��b�0��1���2ss9`��:��<21:��:s����;�@3�;S�<bm&3�?3�8�92s�9ҳ(�>3�>e�b\0�3�3A��Q�%�>q�Ct;.c�?�;ӡ<�B\0踓�Bq���>T];�<T/4w3s�8ѡK;GR~����t�\$�y �&4�T8�{#�ns���AI��D��'�Dg��JTF��5r� ��&S�.�&t�62g@sM�0��5��O4�Ob�O�QH�����P��7UQKP��P�\0�ژ5#N��3U/Qu+O+&cPe�RU7SUGT�51t\"-4'+r+��9E�3Uk(��&2�S��W3;.��K�#��/(ѵA@؞Tk=Ѵ��X�@�L�E��8��(�\0 ���&�S�M�u'��u�W�\\�RC&5y]�U*W]�00`��;Z�KK ��9R[S�CN5�SV	Q�R,�s_Ĺ6�C6@6';hIb�1,61aV/^�cV=N\0U����(�6R~���Il\\3�e��00�\ra�BD�d��:\"�4���,");
    } elseif ($_GET['file'] == 'dark.css') {
        header('Content-Type: text/css; charset=utf-8');
        echo lzw_decompress("b7�'���o9�c����b�F��r7�M�HP�`2\r\"'�\r�\rF#s1�p;��Ɠ���e2I���Y.�GF�I��:4��S���3��㔚Y�u(Ìc(�`h��#%0[��L�����h���C!���E����b5�Ú�������y�fb��w	�z#���1�P��6����Xt4a�l�t�4��g�E��B�#��ja�� �K��q8ڝh�]�����a�2ƼP ��y����i2��3)�U���o�l\0�}��vٛ�r��7ϸ� N2�)�3M�#��)PKj��x����8C(\\5��S\n?��v޷邊8��\\������ �[8�x�#��G���!a�^>�qh]�#����L\\6�#��2��<&7����G��1Hr�`*���7*��#��@-��6D��:�;S:��*�l�<�!ʹ�����1��\r.-��5+?T\0@1?n�\n�\$	�6����0��,ڎ�A�lK��=P�0ʉ1\neQ�%\$�2	eGR��K�0�R�V���{.�Ĩ�>��s��0�,b0��͹3��\noe����r��t�e���j1��8Ģ���+��� /[�Aâ758�4�A@d74�*�0,�8�c����+��i�b��ˌY#.7���GhV��`�7���!����cT�d��y��\n�?���F3�g�ƃ	eu���։�h�:+�<6�G�f��\\4���X�>*�`0LϰMzZ�B#{*x27�eo}��xޛ���:\r��C:&`f���MpձCL�T����1����H���`�G��<_�pd��Ajr[����@2\r#�h�V�D>�u%��3���b:h���kA�1\\ɀG����������C��0�Zs���a����;S��L�0L'��Yk��]��i\r)�v ����m*'7Ʋ�[�>@������Hpzf)��`l����`�׫h�w���f�|��7!�i⥀�Ƞtw���\0p��'��sҿ�b`M���gx\")�=I�\$��C��w��ǩPrl��!5 ��p�hc�@�2 ����^���48(��\$dl���A���Q�,���	E�A�:C���P��E\"�!�F��h3\$h�C�a\r��2ȹ'�PmPI��>���bE��Ƹ�΁ln���@Ʃ��~kD���?�9���Q�q\r�8LC,L\0");
    } elseif ($_GET['file'] == 'functions.js') {
        header('Content-Type: text/javascript; charset=utf-8');
        echo lzw_decompress("f:��gCI��\n8��3)��7���81��x:\nOg#)��r7\n\"��`�|2�gSi�H)N�S��\r��\"0��@�)�`(\$s6O!��V/=��' T4�=��iS��6IO�G#�X�VC��s��Z1.�hp8,�[�H�~Cz���2�l�c3���s���I�b�4\n�F8T��I���U*fz��r0�E����y���f�Y.:��I��(�c��΋!�_l��^�^(��N{S��)r�q�Y��l٦3�3�\n�+G���y���i���xV3w�uh�^r����a۔���c��\r���(.��Ch�<\r)�ѣ�`�7���43'm5���\n�P�:2�P����q ���C�}ī�����38�B�0�hR��r(�0��b\\0�Hr44��B�!�p�\$�rZZ�2܉.Ƀ(\\�5�|\nC(�\"��P���.��N�RT�Γ��>�HN��8HP�\\�7Jp~���2%��OC�1�.��C8·H��*�j����S(�i!Lr��D�# ȗ�`Bγ�u\\�i�B!x\\�c�m-K��X���38�A���\r�X���cH�7�#R�*/-̋�p�;�B \n�3!���z^�pΎ�m�R���t�m�I-\r���\0H��@k,�4����{�.��J�Ȭ�o�Vӷb?[�Q#>=ۖ�~�#\$%wB�>9d�0zW�wJ�D���2�9y��*��z,�NjIh\\9���N4���9�Ax^;�^m\n��r\"3���z7��N�\$����w���6�2�H�v9g���2��kG\n�-Ůp��1�C{\n����7��6������2ۭ�;�Y��4q�? �!pd��oW*��rR;�À�f��,�0��0M���0�\"�� ��\"�ħ���oF2:SH�� �/;������٩ri9��=�^�����z�͵W*�Z��dx՛��֡�ITqA�1��z�Y!u������~��.��P�(�p4�3���#hg-�	'�F�p�0���C+P�����, ����e���N~�y@fZK��O3�v\$�`�C�	N`�!�z�pdh\$6EJ�cBD��c8L��P� �66�OH�d	.�����Y#�t�H62���e��@��~]C�[��&=G��\\P(�2(յ����̐q�2�x�nÁJ�|2�)(�(eR���G�Q��Ty\n�!pΪ\0Q]��&�ޜS�^N`�_(\0R	�'\r*q�P����x�9,��-�);��]�/��w������C.e��y\0�,��	S787���5Hlj(�� �\0�մ����q�I�/=S�� �àD�<\r!�2+��A�� J�e ��!\r�m��NiD�������^ڈl7��z��gK�6��-ӵ�e��!\rEJ\ni*�\$@�RU0,\$U6 ?�6��:��un�(��k�p!���d`�>�5\n�<�\rp9�|ɹ�^fNg(r���TZU��S�jQ8n���y�d�\r�4:O�w>[͞�4�4G\"��7%������\\��P��hnB�i.0�۬��*j�s��	Ho^�}J2*	�J�W��Gjx�S8�F͊e��6�s���*�\r<�0wi-00o`��^�k����*A�,ɸ�䍺��i���nj��2索A\"����[;��n��B^��0-�����\n:<Ԉe�2���h-���2�n�/A�\r6����[o�-��c��R@U3\n�\n�T��=�R�j���7s\"���Y+�\"u�<fH��`a��z�E�����^7syo:!�V���k�m����if�ۻ�/�ڦ8;<e�N�2ͱS�W?e`�C*B��͔�ZB�]����:K�_7��Ċq��Q�)��/�:d�i����Z^�3��tꃥ��t*��\$��f�z50t�UJg���S\r�cX�����\rw7Z�N^`oxP���I��x?��T�ke�� �Jim)�x;�X�����C�=V=���<U��!�0��n��;���~AZ��7�����+Z�=n���{H��PURY����������4�Hǋ6'g��2K���~��|hT�A��1��V���>/�^��l.��SI�.�9g��~O��%ئ��̾�)A|��\n;-��n��[�t,�����Y�<>j\n��N�eP���O<��� q���(G!~����`_�\r~���`��.�>'H�O�2�yK����d:(�,�<�3�:�����+0nUYZ���^�)ww��!�����1����!����mG��ַgd�=���X�[ޢ�<��ߩW�����7���`�o�ҭ��������G���~`�i`��*@��v������\0)�ꐜ\$R#��������Ud�)KL��M*��@�@��O\0H��\\j�F\r����]�gK��i�\$�D�*g\0�\n��	��s� ��\$K0�&��	`{���6W`�x`�8�DG�*���eHV��8���\nmT�O�#P��@��������.�\r8�Y/&:D�	�Q�&%E�.�]��Я�.\"%&��n�\ny\0�-�RSO�B�0��	�v��D@�݂:��;\nDT��< �Q.\nc2��Ry@�m@���	��W����\n�L\r\0}�V����#����-�jE�Zt\\mFv���F���J�p�B���(����1� ��LX���	%t\nM���D���Z���r��Kg´C�[�ʴ	�� �\0Я�������R*-n�#j�#�����4�IW�\r\",�*�f��x�/���^��5&L��2p�L��7�^`����� V�`bS�v�i(�ev\n��|�RNj/%M�%���+�ƫ����߯�'���R�'''�W(r�(��)2�Қ���%�-%6���ˀ�J@�,��ֿN���Q\n�0ꆐ�g	��\$���*L��.n��Q%m�\"n*h�\0�w�B�O�\0\\FJ�Wg� f\$�C5dK5��5�aC��4H�(��.G���BF��8������ E����.��k3m�*)-*��[g,%��	��7�.��!\n�+ O<ȼ�C�+ϫ%�O=Rf����(���n�Y��ϲ�%��s�1�6�3;��ObE@�NSl#��|�4\0�U�G\"@�_ [7��S��@�\$DG���D�5=���K>r����\r ��Z��ֱ@���H�Ds��n\\�e)����b�'���BPGkx�Z���#TK:�w:�a2+�aeK�KR)\"�(4qGTxi	H�H@�&@%bZ����ܪ)3P�3f `�\r�I6G�%�/4�v�\\~�4�ݤ0�p���,��E�)PH8k\0�i��\$���3I4�P�V'F^� �'D��R���+Q�`�����8\n���D[V5,#�qW@�W�0�O2� �t�\rC6sY_6 �ZkZ@z3ryI�<5���.W���ҷ@5�Ģ#ꎄ5N �~��ȥu�\r����)3S]*g7�����ҕ�_ˉ�_�ĸV\nY�)a��1P���FI\r;u@/!![�e� �(CU�O�aS���KP��t3=5��O[�f:Q,_]o_�<�J*�\rg:_ �\r\"ZC��8XV}V2��3s8e��P�sF�SN~�S5U�5�z�ae	k�n�fOL�JV5��j�����Z��lE&]�1\rĢم5\rG� �uo���8<�U]3�2�%n�ַpr�5��\n\$\"O\rq��r)�f���7/Y���p�I#`��Kk;\"!t���h�usYj�[�R�\n{N5t�#NΜ�o6�X)�c6���e+.!��ߗ�\n	�b��ʒ�t�Ү��\n���j��(\0��2��4erEJ��d����@+x�\"\\@���� %v�����{`�����`��\n �	�oRi-IB�-���Nm\\q@�,`��Kz#��\r�?��՘6��<j��f��!�N�7���:��/�Tł\0�K\\�0�*_8L�m�^r���V�w��\"��кB��Q:5Kn���v\0��xt�;`�[��	�B�9!nv�<ۢSҏ{:P�p�r	~����1i*B.�tY�>\r��S�*nJ涨��7{�=|R]��ռ��4�i�U�2�2���Y3�c>a,X��3���9�\$�<A�Q�&2wӭ3���1��/�i����j��sO�&� �M@�\\���گ���8&I��m�x\0�	j�k�ۛE���^�	���&l��Q��\\\"�c�	��\rBs�ɉ��	���BN`�7�*Co<��	 �\n�ν�hC�9�#˙ �Ue�WX�z0Y�7}�c��8?hm�\$.#��\n`�\n���yD�@R�y���@|�Ǎ���P\0x�K� w�5�E�Le�@O��u���|�R�2��%�aA�cZ��:�<d�kZ��y{9Ȑ@ޕ\"B<R`�	�\n������QW(<��ʎ�革�q�j}`N���\$�[��@�ib����f�V%�(Wj:2�(�z��ś�N`��<� [Bښ:k���ʚ�]��piuC�,�����9���e�j&�Sl�h~��N�s;�;9��u@.<1����|�P�!���zC��	�	���{�`��Q!���5�4e�d�G�hr���P���}�{��FZrV:�����Ŀ�Z����|�P��WZ��:��d��~!�}�X��V)����p4���.\$\0C��V󁺩���{�@�\n`�	��<f��;dc'�\r��,\0t~x�N����y� ˽kEC�FK\"Z�@\\C�e�D.Gf�I�8�ͤ���CĥY��q9T�CU[��z�^*�J�K��VD�؊��&���b̷KK+��Ĳ�,C����,N!��\r3�Y�P�9�\$Z���n�\$S��5�\r��aK��E��n�71Z���3e��J؜x5�Q�.��\n@����ǣp��P��ѡֽn\r�r|*�r��% R��蔊�)��#��=W\0�B���z*�W���MC��_`�����P��T�5ۦWU(\0��\\W��&`��a�j)��V�W�ʧ�b�f�O�rU���Ǽ~#c�Ur�5�`���Gd����P��fW������Yj`��ǌ\n��G�>K�h���ǿ��[Mf�g̗�|�\"@s\r ���Ӷ��iU��m��~��f�K�.x�t���X�P�����׬����-���!û�~��+Rw�*©��ܞ�K��\\�-F/bN�s����Ru���i8r�\$\"�8j�Rn��5gf�@�FSM�S�c��5C��*y�C���cU�@o��esI�H9QoCQ�������=c������{�c8S�v!��;g�L�5<	�#�z#���qL�������V\r�2\$�J/{z���m��i�nG�?~ĕVu�0wʹ�=p�I��HĀX=�����t��� -��MJTP�#U��`��/3\\?�L�����y���*p�8���:��0{�k���2�&�P\0p8����Y�\\'�%�����.\r��,ƁJ�����/_,�4�~��,�!�Rn%x@��0Fdt\0�4����\nK�\n��G\$���Y	 �\0@,)�%:\r�]�L2\0�PV C\\Ѧ,B\r0W�\0��Rr<�UH���Q�Al��'�\0 �T�)�(c�\\I��;��/�ik��jV^�p��-PP��)���Hx������	Np&��\0d��8�':#Q\$�Q=�\0Wn�k��,�aS�����qbj\\�<g�9&�e�1����eb:��N|#������φ� ��n���h�wJ<8p�.���9y����A41auf���4�u\nL��%��/�:\r5�%H�jA^����s�\n���|�x��X�� �&�f���얐��@hESЕ^@:8@\r�n��^��H\"�&\rC�bq!�:&�AC�Jj�&	�&\r����N��<	�p�4��Tiw��-���2)ȫ�5O�B�3���#\$� ��e�VUB���v�d���xS��7e!�DF�O`����f.Q0D�#�\\��%��J|�\"�N\0�l`E�W���\nt��UR�(��L���gCcRy��T챞:j���W���E�.Y�\"*�25��X\\)d�\"lo'zJ��DJYX��\"#�����	G���>�)��Y�&2�,'ڄ�M����6�h���Z�N2ȷ@).��#�B��n;��Ga��R+�O!|�Ê��\r�ܓ:�������v[qD¨W��r���c��G�\\\0'WY�ؤ{�ׄ~2n\\�\$<�XQ2Dw�DDxCfH�,~厏�T\n(+A\"�.B�`���ǘ��L[E����	�\"Y\r�C�֐��`,z�XȔ?o��E�K\"��}&@`����i<���Uڹ\$OS1�Y�j�^8\n��8X����_	� z��'ȥQ�s�+c���X��L-ß���\rjD�5}Q���C�L&.=�P0>H�8H�����G��K2�0��B��P7�Q�%iG��h�^�&5�5�Q\"�9ъ)�P�BS�\n3&�ʐ����\rJ!HJ4\n.{�W�\"#Z�\0R��\r9+2���DE+ie2�Y�i�y���&���%��p�������K(��srp�%��`/65�b2T<���a�#�]��-Ի��.!�K������maJ�[KT�\0l�PY�'4�&�膗~���1t��4��2j�'\0��V(��\n*+W���ci4cʭӞ<���X�/�~�ɢ��L�&��2�b23RҐFT���R�%b<UX��������V \"Z�� p��\"�[�@m��A1c���k˔ �p���-�|��l�f4��ю\0�7��]�OI��@�����3r�\\Dd9*��\r3>s���V}���U,�y0���g�\0��\"&�����\0���P�BHCrh����i_�� ���`-pЅ�6J��/��1j�.����kYÎ9�(} r���P����\\�gu@��\0w�-�0�'�<�Ώ�\r�-\r�˖9��r+���Iޙ�+�&�����-=�|��yeж(	\r�H�z���>��N{�����0V��-�!�t���;ກ|\r��@R�\n\0�Y\"����\0��}�s\r\r�A�V�� }�d�H'8 0����9�1���8�\n؍@	P�&:\n�F�\0d�\0��5����3r�\rD�C�1���3���8��	�k�N='�70�QP%S�\\��:B�pzo�D���6�B�H�R�(�4��͐A1����Iv�q]��joD\r#)�#%c�ɱ%��%��_'B�O )x�c�a�=�/���6H�j��>,r��o)G���u)��#�&�#Is	I��~��_��O��J~��՞Yb%*?\$yP0��(	��%㠋�c�<�0	�kPt�B�����3\"E�X�2q�y�-:�@�ʀ���.!�qDW�0���* �(�Z�+/d��_g=���(`f��P���i���b�1x���b�>�pdd��T��E<[e)����y(}E�v|�]���OCQ�r�H��\0�A��W���J`V3�@B�I\rm�I�uISr�Ґx	 ���r�I��HJ�S��UB�\$�@ت�\"<�pe|�1́DD�*��DZ�e�T���5%G�O�\0��0I�(4D��m���פ	V�,{+���P�����A���U�]U�e��s�I+@���\$CMM]��->a�@Z�М�5����3*��v֪���yU�Cebj�ӈ�\r�HN�6�iZ�>V)7u�@��Z�H־�D|��5�Б�J�M�A)SЕ�,�i���f�l��PS:E�M���삩52p:�{�i�i�_�j���p6�.�>H(n\$�1i�IK��֪�3�V\r�]^ĳ��ꮧ\n�)0I��`B@�b�j6>h�F�g�/ y2��A3YG�� ���z�4�����k=��R�Z����AYΪaW���*�	�(5���!O���2ss'xg�x\0��\"��\n@Ek�\0�Rֳ��%Ց��'�B*f�Bnf�Sfצ5��+ʶ���#Bݯ��%RX�¶g�@R4�`\$i�e��;���	%�ʸ�(�|�ȇ\0��������]:�gԵ>��m~��\"+)��?�]������C0��\$S�<����Ѕ�+\0�֭�3��r��;H��iR�>�h�vg��%�����Y����RhT�%�N��l�c���d+a��E?<}�Tfŉ욢�\n\nJ�UG	�sv����kP���u�!s'\$��;�0�E�@������&Lo\$ mUM&��\"�����f����w匄\0�\"��1��M�;���Y�`W�B��\0T�f)X�tT�x�\0000�V�,��!s0��G\"eQڏT4��\$�[�E/eO����<���-��H-0,�d?R�,r�@gG��<���`��sqe��ۢ�m��H��L.�]��HB��\"��\0X��B��7b�s�\n��L�7�Z�Z�\$�����\rפ5���w�Ҟg�/]��h��;b�7���M]4	�ҫ���dFD�1�z�ca��o�Պ\0r�;��P� 8U�!��5�c��#]����R�@ƅ{�'�i@:���ʴ�ɧn�kX�©ֱ������8\n���s|�A\rQ�I�b��M'��PU���2�8�#�;�KO_}��#��wi=��#fc���������֞�ݞ��z�����/&�gw�m�X�	`�d��[�i�`m�X��b},�|�+�����ts�+�iiԇ�t�\n�>8()��6f2���#d��˴IH(\0.��#v/9j���!��2КE/:H/��A�ybj��\r��zV,p�w�E}^``��?X�-�\nz�*�UD~?���\\Hc�U�WXz\\0�!�rJ�A�o`�����Ba��!�C�!��W(�լX�P�.���È`�@ov���f� in�@U���U�<p�GN�U�𩆙c��8�7�O�-��������ABXr�vų,�H�؍58��ո?�cb�Cʸ�5q����&>!�4 �ͅ(q��	I��-Jm\0>�5I�D�m�B�mD�\0��`2��yN��D��4N�L`�����0x�_~1\07����k����h�RO�`j_�ŭ���� ���m{�p0��!9'�K-�\$�����\nj~�h���Q��N��\0��q{��x�\"��2�/q��iT�F/��*V@*�g�-��� b�c0	H�0\0]�@�H�U�:U,V�Gd�?��u�L}L�9��u�:\"\$�Tl���e��ɐ;��V�\nL�!+�ǆT�1�j,�}R\r-[�2�6U�i\0d��/Ҏ���@r�.��PF	��g���<����S��&i\r\$�R72�>fs#��3��7UNȵ\"ϱH��+�9�[8�	B� 	�A��!3�_�Z�5��3��%�r��W9y���\n3K�|��o5gh;���d��\r��	D��3R��g��L\\��v	IGB�_�8`���<�a�?�s�q��䘬�b�2�N�(��u��`L���Ӧ!U�>��\r�e�~_����!�S�t1'=\r�C���Q�r���\rC��*᠗�f�3`{� G����|U\$n�J���3H��;�R5ؖ}�Qw9�B���=k0��F��Ǻ\$1sb���-L3�C\0w!ʹ�P�&[�#0�طPS�\"����%r�{ZA���]�DE%�)��T��{@s����u�e��R��Ԉ�53����#�>��<�\"�A:�t\"�z��KH7�8}�k������3�'�N^���Vh\r�Pj;֯���u�f.����\$�yW�|U\$�:�������qĪM�SŞ8m2�İ�P��.�'��c��,��R\0K�X�Ў�����]��q|��Z�P������,�\r�\r��C�Ř�}�u̟5���?��z	��N�k͉lI�pw3���KMj9�[{È1i�s��yN�Ýz���qv�eGÖq��\"r�媩������W���\r��μN�7�C�+�@FJ.2El���A8��{�Q;n]&�H�\\�>N�d\0ctʄ�Ў�?t%%�v}@ƴ��ZL| y��X/鳍��n�ք�SR�mxW�/�Hr��l�o���ԩ����[�#�� F�k�*��~滓�t���W��;X\0�~����r�i颃\$V��7;��4;F�\$�B��`;6���\\n��Tw�j��:p����t�\$	7i��ֿ����7+Y!�5.#�ۇ�U���ۻGv�۞(��W��*_Sj�c]��`e��nyS�m�ܹ#������i�52�s~G;s��?�F�V��̈́ۋ\0N�,H�@T'L�i@�/�Y����\r\0���xUx�d>漂����@3\0yH^o\"u�ā�&͇x?Ti��\$/n�T�5�ŉ�	�ΰ<����d1ȋ����)��y�|9�2�T98a�/�S�X�)�Q�H}����.�g��K���5�Z����=��pߎ0��ô�kJ\n��L�f�����R	�EFP�d2 +ȥ�q9d �܎�yZ��!<	��j��\$I�W�\") �\n.4��N3	7|��暇td�\ne{ӡ��z��Th��a�nx�,%�/39��rw=\"�]�t��<1���|�\\n�W�~�XA������h��d( ޚv����Loc�8�l�9��W7�}w�8Ch����w\"PZ��]��u��H Nk�,�����.�����&@��\$��w�/�<��O����n�|<��H��RKt6H��2OD��!Ds�¾�0A�4F�ӣ�u(x��]q3M;�^R����t�� ����)\r��0�9ޟ2���g3r�=�L\"�	¾��pe�0H�-=��ㄊb6��a�,�h,�[k�{[�E3�-�I��,��ҹ����ך�P	c:���u��\r�]�M��ؤD (^�eƿ��,��iG^<6��H�jBWK�<ڸ%⫎�w؂l�.��PT��FK+��f�&�v��0��]@�Qx/b�vc'\n�A9�xb����X�ԧ�]y�M�}'\\�)/Hgm	�fϣ���Uz�6]Sŗ� ߬&�<��n�zt�oN�z+��f\r���>Y�{n�~��\$Ԇ̳0y���g%)� �=&�{@t�w�irK������濺zٴ��)�y42��Y�>�V3�^m������|�I�x���T֣�z�wk�����ʉ,<k��\$+�1<�� _�����d���}g��9F]�k)�|�������e}�iΨ�g���B�������\0ڃ?�۳��)HBH��DE�e���V��mP�yP��(�(�1}����W�05;\\\$+@��<v_\"��2@bM۶:�X��V���]꾼(�g\"�s\$���B�3\\�xDp���@D�'�X�*�N�����\$2��/VeM���y�r\0�����W�VL��e�L�dӸ���~�����[K��p_�@^�c�%��)wߩ�s�Hl�\n���w��?VO�H�j�&l�_��YŮׅ���9�b'Q?2[}���{M\n����2\\xml�PѦ�3�.���fD��(+%��D\r�r)@���h^����If�Mlu�x�\0�	�ڿe>�5�5�S��-��;�_��@���X��%,ɿ�f�|���@+�O�|J4P|�	-��Ś�u�h8���`�~�5�l��Q�;�o@����G�D�A�_�\0ӄF_Z�ٱD+:� M�u}�L�\"�J���(�L��_���d�a���o����	?���|�sD\\1A^\\�\\G��a-\\nˣ)���3e�܄�'Q��zf�v}q���P�7\0'�����e\r:�Up��y��x;�_YT��Yl��@ղ+����Mz6��d�)��`5{0�W\0��B�	 ���*U��Z���@�}l՘-��8�p�XR`8����nG( ��\$�%MT����\\���ڪ�FE�dc��3��	��\n�<&-9Jow��\0)?Ƥ0x:j��|i��vAT�;I��Q��AX&C裡M@Jl��L�(LP��3�� ���0�\0��H�+\0	<�; �N��(;6H���F ��e �p��6�/�sJ`�*��ڐ	�L3�2��<�9\"	���^lF������F(����B @��P܇;���F�v5l\0�ݠ00t�k���>\0O�U�<���Xq07�BF�8���K�#4�4 �%�wPN���A�Q1�D�O`�AHBp�p�`2^��c��	P,=���b�����F�C�\r�2���`8@�Ud�1��I��A��T�3��\\\0p1)5)F�@��D\r%VAd���\r^�� Âh	�#�A�����`��\r��������A���\0>���b	��\\p�AK<�w\0��\r�B%��W�f}�mH�j�a��dt\"0P�|���?�Ԇ��6�#�?�O\n\r�R���\$o��d������}	S��䟩\n��������o��[\0	��?����d�r�\$o�+`X-Y.��V��I�G>\0V�PM\0W��G�z]�\0TV��d܀_��a@-<\r��\0Y�+�H���-����f���tu��'b�4O��P*��f���Ry*9�8�:�G�D��3 34��5.F�V/0����R��Й��HaCF`g�+־�\0�<h\0\$���#��m/���㐞x�CAS��Xҋ���p��bc24���|��g�D�7I��J8@����|�6��-p��������*B���=%��������ԯ���r�	^CJ`\0sEpi�h3���`���H��p���U`�e��&�1�%\0V���V'X(W`��\r��i�F�k�W�n�q�����DQ#t��Kt\$���Q���\n\0���B���X<�Cp�\0K�(\0��C�1�BFIq�)q���� C~�����q\r���C�\rؘK�\"���j�ZZB��v�#�< �C�\r�u3���	�+D�钲�Ԍ�;�-��");
    } elseif ($_GET['file'] == 'jush.js') {
        header('Content-Type: text/javascript; charset=utf-8');
        echo lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0�<d4�E'�\n#�\r���.�C!�^t�(��bqH��.���s���2�N�q٤�9��#{�c�����3nӸ2��r�:<�+�9�CȨ���\n<�\r`��/b�\\���!�H�2SڙF#8Ј�I�78�K��*ں�!���鎑��+��:+���&�2|�:��9���:��N���pA/#�� �0D�\\�'�1����2�a@��+J�.�c,�����1��@^.B��ь�`OK=�`B��P�6����>(�eK%! ^!Ϭ�B��HS�s8^9�3�O1��.Xj+���M	#+�F�:�7�S�\$0�V(�FQ�\r!I��*�X�/̊���67=�۪X3݆؇���^��gf#W��g��8ߋ�h�7��E�k\r�ŹG�)��t�We4�V؝����&7�\0R��N!0�1W���y�CP��!��i|�gn��.\r�0�9�Aݸ���۶�^�8v�l\"�b�|�yHY�2�9�0�߅�.��:y���6�:�ؿ�n�\0Q�7��bk�<\0��湸�-�B�{��;�����W����&�/n�w��2A׵�����A�0yu)���kLƹtk�\0�;�d�=%m.��ŏc5�f���*�@4�� ���c�Ƹ܆|�\"맳�h�\\�f�P�N��q����s�f�~P��pHp\n~���>T_��QOQ�\$�V��S�pn1�ʚ��}=���L��Jeuc�����aA|;��ȓN��-��Z�@R��ͳ� �	��.��2�����`RE���^iP1&��ވ(���\$�C�Y�5�؃��axh@��=Ʋ�+>`��ע���\r!�b���r��2p�(=����!�es�X4G�Hhc �M�S.��|YjH��zB�SV��0�j�\nf\r�����D�o��%��\\1���MI`(�:�!�-�3=0������S���gW�e5��z�(h��d�r�ӫ�Ki�@Y.�����\$@�s�ѱEI&��Df�SR}��rڽ?�x\"�@ng����PI\\U��<�5X\"E0��t8��Y�=�`=��>�Q�4B�k���+p`�(8/N�qSK�r����i�O*[J��RJY�&u���7������#�>���Xû�?AP���CD�D���\$�����Y��<���X[�d�d��:��a\$�����Π��W�/ɂ�!+eYIw=9���i�;q\r\n���1��x�0]Q�<�zI9~W��9RD�KI6��L���C�z�\"0NW�WzH4��x�g�ת�x&�F�aӃ��\\�x��=�^ԓ���KH��x��ٓ0�EÝ҂ɚ�X�k,��R���~	��̛�Ny��Sz���6\0D	���؏�hs|.��=I�x}/�uN���'�[�R��`�N��95\0��C������X�ْ�6w1P���u�L\0V��ʲO�9[��O�>��PK�tÈu\r�|�̮R��pO��U��Drf�9�L�cSvn��Qo���@o��(��ްàp��a*�^�O>Oɹ<���e�������\"�ٓ��P>��H^���	psTO\r�0d�{�Z\$	2�,7�C���!u��}B�^����?�D��ڃF�ݱ����H�Ι`���'�@J��3��|O�ܹ�B�Mb�f1�n��@�1���(ղ����!�oow��f���)I�L\\[�����8[1)��!)���u��~�c�-�6-���y*	���>\"�m�61��ӕ�.��~�*�x��諍q��ǚG |��rl��O*%����݅�A�bRAx�g��D�f�V\\��R5l��ޤ`��5`��w�|���Sg��O���B;�Ϯ^LÖ��W?�5 ��ac}��s�ݏ�I��A��r��ݺO0�;w�x���P(�b�m�L'~�wh\0c�¨pE�߲:C�{g&ܾ/Ƒ>[����ۜ)	a}�n͡��wN�˼�x�]V^ye&�@A	�P\"� �E?P>@�|�!8 �Њ�H	�\\�`��@E	�Â�4�\0D�a!�������nr쯜\\���8�o`��H�f�����&���̒<�r��(jN�eN�)�6EO��4�.��n0�������6\r�� �\$����\$�� �N�<��|αN���j�OY\0�R�n��`�o���mkH����*�-Ϙ�w	Oz�NZ*ʛn�O�\n�#�n�⏓p[P_�b�������jP��P��Г\0�}\n/��Ӑ�������П	o}��S'��`b����\nPd�p ?Po0sq\n�:b�L���Uu\r.L`��SP���1mq���~�]%&ʚ�Q��� �\r�D�pq��pV|��f�8\$�p�&��ׂ�F��&����m�O�w��G	��1/elր��D\0�`~��`K���\\�b&�Q�Q�`ʾ�A����V�E�W�n: ؓBƌ�\r�*��l\0N��D��r뭦���[&G��h�r�H4A'�bP>�VƱ��M~�R�%2��r�m��\$�\0��2�c�����Mhʇvc���}cjg�s%l�DȺ�2�D�+�A�9#\$\0�\$RH�l��@Q!��%���\$R�FV�Ny+F\n��	 �%fz���*�ֿ��Mɾ�R�%@ڝ6\"�TN� kփ~@�F@��LQBv����6OD^hhm|6�n��L7`zr֍�Z@ր@܇3h��\$��@ѫ���t7zI��� P\rkf D�\"�b`�E@�\$\0�RZ1�&�\"~0��`��\nb�G�)	c>�[>ήe\"�6��N4�@d���n��9����ɴD4&2��\"/��|�7�u:ӱ;T3 �ԓi<TO`�Z�����B�؃�9�0�S>Qh�r\0A2�8\0W!�t��twH�OA��\0e�I��F��JT�4x�sA�AG�J2�i%:�=��#�^ ��g�7cr7s���%Ms�D v�sZ5\rb��\$�@����P��\r�\$=�%4��nX\\Xd��,l��pO��x�9b�m\"�&��g4�O�\\�(ൔ�5&rs� M�8���.I�Y5U5�IP3d�b/M��\0��3�y��^u^\"UbI�gT�?U4�N�h`�5�t���\r2}5-2�����W��(�f7@��e�/�\rJ�Kd7�- Sli3qU����z�\0�)�\$�c��oF?@]LJb�Dҿ�0��s?[gʜ�%��\rj�Un���^��R5,֪�t�FE\"��xzm��\n`�-�W#S(�l	p��%CU��辚�F�&T|jb�Z����8	��/4L�*nɦyB�:(�8�^9�8U� K���{`Z���\nF�\0Cl\r�'(`m�eR�6��M���B���C���6��v�����n%#nv�D��jGo,^:`�`s�l\r�_���X5CoV-��8RZ�@y��13q GSBt�v�Ѣt���#��bB������]��#�p���fZC�Ĳ����OZ����N��]�����sl�Ԃ���EL,+Q�@Yw�~9�I\"�8!մV5�&r�\\�7��W�&�ܼ�[\r\ri\r��~L|��d���ܷ�,��|i��@,\0�\"g�\$B�~��!)5v0�V ���b|M\$������D�f\r��8;���}�f��f�����icԄV0,Fx\rR��`�a&nȧ�QB.# Y��>w�g�����E��[�Ɨ�X���~RO��Y]8�]rK}�-��?�8�v�L�@�~�A*��f���J�M��tג���-v�[#�xL'L��>�l�8�Pg\n��\r�Q���ѱ\r�M��\":xw����\$b��-������=�kRXoQ乇9;��ˈ過��sՃ�͋�)���~�geB�Bt���,����,����K���y����-,mӀ���+��07yC��˃�Iz�ƍ�Y��^GGW��u�v0#kX��RJ\$JP+�6x��1�8���Y�g����{��?�\0�X�\r�	XF��W��ה��V/��̓dIg9߆�і�y��1��-�G�X����@O��R�y����!�GuY��5�ZF\r�㕵-�\$�O�e�u-��ZF��Zd��i�9+�쵘`M�z��\r�ҫI��y��A�Vp�:��O�J��:�V:�#:��:c��{��k�l��Zs��W����P0����#�9g@Mc�zw���[9U�\\k�����6��9Ӆ� ���y�,�����f6n-Zu���f�ً�c�,����[o�[g�d� �:w#��!W\\@�n�`�߱�\r��ɡ\$۟������\$��%��ߡ۷�z#��\$�imY��c�ɂ�k�I_������y��L���Ϲ�\$�`V��[����F�2C�8�\$��������ؼ�����G�[����¼���=�U��υ[q����K����Y���݋�Q��?�8���aX���m*G����\\��?�U�\0Ϣ���KĤ��|CR�͓�-����|ɜa��e��RY�ƺ饘�ܒ������������PJE��=��u�����\$�{�8�X��{����ŏ����ٓ�ٗ��ՙ��\r�������Ͱ٬&���Y�ҹ�(ټ�M2)��V u7\0S Z_��o]\\�|٩Ec7��S��΄[���<��<����;��-��i�� �}����l���!�,�}%����-۬��=����Ӭ��=��Y�8���PV|���zE.����\r�����bLfƸ��h*;�	ַ�;�؇�Q{��9\n_b\$5��l�UzXn�z\0xb�k�M	�2�� Z\r��c�|�ג/��}%��`�N�A�\0�*=`�F���^Q3�W�X��<���tR>r�`u�ģ>i��zN���اÝi����\$\0r���s����^C���>U�5���^a�)��	��J+>�uB��@?�J�-H���OJ'�-Tʀ�T��oUh�F��{��ԏJ[��N��V�oJ&S�B\"I^5�I�2���T���龽�]\0��\rk�L%�}�t�۷~I0�H|Pk�L5�_T�<�w��=<�x\"esa�K�\"���JH��+�U�a��'Y�~���7�)W��<6�=_�N�h�?6ܘ��y�,����a���w�\rİ�#�-V@�k��?i�b*%�޺��p?����yЀΆ�p��-p��|�n���Ca�f�8A�8�+#\r�R�@n����p��m�~ۈ{`�H?�v�*%�Ǽ�v%��G�`�`�Z��.���,�6�z��U8��|�y��V�����/�p��^��פ�m��]zcӞ���\$�IB0�|����@���pR�\n�j�9 ��G�7���읤#p߭�?����'���=�6H�lψ.�Y�OY��_V�G����O]I����=��x��\$���=�|Ϫ{��\n��<;�{:f^L'S�A1%�8*�^��p75���W��\n��\0��S⟕\02\nX(�u[��rp��B�0ڭ�x���:n	�ZI3�C����{�[��&�C(@}�r���w2�闌�nt����{C�ɆY!\0�He>��P\"�9t5�o���!�\$@\\7SS\r��C� P㄄@��I���nhG����	I�S�`x�7�0b+v5�^g�r%b�p�U��%)<+�S/Z@ �4!��j��8��\0�vN-6a[>�X�,�e\ned/�PX�`�}kOR�N���+�1O\$�π�F6B-�:wڨ�N��T�D>��x�����Y)��n�1��&�7��}�&xZ�\nޖ������W��:U@��a�⺃@��.�R�hbcT\"�����x\n� E���|߈�\r�-\0��\"�QA�Ih�\0�	 F��P\0MH�F�SB؎@�\0*��9���s\0�0'�	@Et�O�����Cx@\"G�81�`ϾP(G�=1ˏ\0��\"f>Qꎸ@�`'�>;���l������82>�zI� IG�\n�R�H	��c\"�\0�;1ێ�n�)���8�B`���(�V@Q�8c\"2���E�4r\0�9��\r�ԑ��� \0'GzH��5E!#���\rA�JЉJ�(��FC��&�d� I�\"I�V솣���G�SAX��Z~`'UA���@�����+A�\n�p��i%��ѿ�G�Z`\$��������>~?�E�\0�}� �<Q����'����E�w�ئ��#\rɂ7rQ� }�'iMI�O�0dm% ��Hʰ\"-h#��XF��M��t\$�!���R���t�,(�H8�8�!J�5I�x��r\n�Thړ~Pe@&eg\"[hؖ��4����|�2�z�D��lw#9	v{lb��/~\0���&I8%�,�IKA��\0�����/GYK�*�>���O/���2�t�eھف�P93=\$�X�d��-�&��|��#154LU���G.�i�2`����M.B���\00036�ISJ�-�~�쩦�jF\\3	o4�u	(@a3�A\0�c��`�P( ��0\$���\\}/d������\0�-�3�%b0\nc�z`��))%*��6\"����ٖ��E4��F�q���J����d��(�Ӏ����1�iLm�2�A��.)&q@\$�`L���2Lrse�� �.�vss�\r����i�KQ�󤙬 �0()�|�Mb�tU�9!�ED	�(	�`8*pa<�����80��s�\r� N���8O0�Ξ���d0��OVx��@'�<�Ol��J)�	�~}���\0U=��O�'Ňd�~\0�Of��X�H�	�L��Ҡ(]'�@�EP�LW��E'=��\0�'�\n��N�\$iI��Zy�	���>i�OH6f��'�߁x�.\"}@��-�wa2vӅ��A��L>����<0/����P��B�����͢��T���\n���<sSQ~|�ӂ��P�f�i�O�φ�lq���9T\r�����ѕgÄ���Fӧ�%O�(1�h⺶n�m�v�;�|���g���SaF��R��Ȥ�Nr��9z�%&�X��\0007\"�2t�-\rh%fŦֽ���3!�\"(�7I�\$s/ �-�7*J\rΕC�Lxw���֗�铴���(Ҫ�B,+�h\n���f\r�F�7Rf���*�:�\"�Δ4t�P�i�X�����*�\0P.(#��+H�oJAG���q�.57�+N	:-m`���&��HJO�Uvi��\0�\nGN:gR�n��2i�)}#���	F駩�>d�`�q������H���ƕe�5J);HQ�����\nHϓGRW�Ԟ��/�Jj�)K*UR���i�b8za�.�����RG��!4ͣ��@9����c: E.F|��T*��s�<Z]_O�i����\r@�2��qTlVUk�CQ\rOe��\"�\n�.�T�EUZ�Ԡ@i��^�ܪ��L��aMUB��V������'�U�+Q �V���W�m�G��Ժ�u0��*�P�T+�!u�\\�kV�y@Ƥ�j+��H��䁐�\"E��P��,�`<�H��Ք�p�ğ%	l\n�K ���\0�\$T!8@�@�2����h��4L��ŝ+��&����,�|��\"�T��Q霋�b#w)umŵ[�ޒ��)E}��[���Exd�)p����	n��-AK��1}W\\IU�nF^�\n��` \$��m)�oZ��	P�D�P�V��D �r%�R)��bұ�l�^�w�)JB���-K�D.1��8����\0��;� le�,L(\"m�N\n�Z��K�����gH���e��\0��\0t7�]��Kk\$�yN����X\0�6�(Y�������f�\\\r�K1y�,�`0��qo����\0�h\$��\n�_����dR��zE���C�h�<Y���p!�\0ro;������'g'*�!��Y�Xv��%�K4R�V�\r����Z�}Z�\r�o��mpN]N��5��xUay��\r�j��W��k�b�~��+m���edyٯʰZ�ksO�4;T���a�l@4[��]�M�7n 7�>�6���ϓ��=�h�*�0HΫj\$��[`���,����y	>��7p��D\$��u9�H ;�������R��~�0[�D��H��삕6�ܐ>-Lxj�Z�k�NȢ����n���dg�;�C\\\n�Pb[�h)3M�c�D4�0uR�#bP��5�:�a��EqH: ���:�.X��?�c�9�%n�K����a�5��J�`�7X�\n�q=ȿvr�E�<�(~���CȷPQxH�bK�ܪ�-]����\"�Q��C�U�.a��Q��v&�� ��7�]Ĩ媻�>�.9\0�=K=)���T�� ���_OX��5�!�b�U��h���AP�-����\r��%zPޔ߀<�x�����c7�|��4q�����p�C<�N���Y�5ь��)�澈��}AN_�RCTx�F�*�3���g���.�`*��B��`&�T�:**�7ƷE�W�R�\\�c�W��[���Kb��\r�o�Hr�����u 2~/խ�	@����aI� ,%b �\0�¡+{��[�,`_6�7��.�@̆�)?�m�m�b�a\n�v�������]`��W�8��!���W`��:�Fpo-`7	�\re��XXzK�I:���bD�_�5�>���ŗ��f+<Y��vg��,�%�H\\  d\$@��q�\n��A \n��6�8F�'|�I��R���T�{s�m3��8b)��	@���Lc�M���F@�#Y`��N���DX��CxzYc�0y���3hDZ��6\"�t\\7�SE;���U#�R^��ީ�s\0Cfb�ܚ��rrI\"Y�	�tå�8ZB/.�`�E��K�|����b��\n|_�}��KC�.��� p�1:����#Y\nTC	%,,��\r#�@�+��dqŁ�\$���{�D	\\J\0񒫇-`m!�|�g��dz�VI��vv&��A�`���MH\\I�����|E������j�B0ۊ@ѡnU��K��ތ��>����]ݸ�h��i�X9upr����a�\$7�v��Q��CA�>1����xif�R���7*�;8%���\"��Ʉ���w�P��TB���yH��'\n攏bظ���v��T5xcH\$�\\��ۏ����X�l��K���a�`���#t�Ew�gh�1�� �z���p���4:�\n�C��2��H�K<X	(!J��;�㏨���,��u�3�y�s�M�C9p��wz\0��ՠ9���ǈ�x�ǃ��1��B�������ي��`r�)=hLƂ�`���?z9�E�?���J���1�����Q��R�<\r�L\n8(#��r���p>��L�Q������|���\"4�(��*���8�fpiWaQ\n�Q��*���\\0@H�;�V��Y�Ά�����OZx�<F��'�I���A\n<�]�dP��_N�T!�\r˧���@*~І�B��=�%�z������;��:���AB}��&�l��c��h�`T��O�))�\0�y���I��ۦ�8��Ny��ј�G�\r\0�T�\"hn�5W@}�����Ն�B�}ZkV���Ф�y=s�	z�Ӕ����;\r쌚��,�hT��i|jza&�ր\$�i�S°�Hi��>I�B{Z*U�Ә�I�n���O�}��XMs��Q��8��I��Њ�	��v&! �k�@���#��<���T�Z�.����j�Z:�	^�B�}Y�����v�O3BTC���6=��k�eS��~���?]ij�O�Ѧ����m,\0}�!���mF!�[J�.��g��Ul�ZP٦����O[;&����]��Oht	`aILA��k�bki�N�vY���m:��v�v���k�g7���)���>��b&�؞��p�\0�5�I����]dp=�+:;��)� ���Dx@^o��ѸA���L�'�����t w�&U�g��3��B`/�=����'d>�/�dbF�\0�w\0y����9���n�Z[��6Tu���b�Z���~��~��\nzd'�@�Ra\n\n@�G���0�;vS����={��~��\0@_c0�ov�1�~�x����������e�\0p�o��>�83�|�pp�<�Il�o˄��O�;� ����%8Gx.>�o��O=^uLG��\r�N7��ݶq8~&n�5��l��]ڀ��������I..��4ओ_ۼ=��x���P������I�����5�]���[\0_�\0̓��  �<:��e��o������� �B�y�/��Eq瑻���f'�J�w��#7���N�x�F��(y��D�7�\\��'��Y2�˕�?߯)9e�nGr�vQ	�.�/�.Y��ܪ�<�zkMޠ�c��M��B+�\"ہ�\r�g�l�\0^\0��B@-	T���6�1����\n�����P�@ \"\"��@���F���0��t����U�\04��!��_|��(B\0Oc<��'����t�\"m)�TW����F ��P?f9�����C��M�mk����D���ސ�|���	�&��3�`�dΞ���\0O8�y�@��\n\0I?@@�@�/��O��\n��0���<d�\r\n�\0���H��C>�k���nm_:Gb�\$�\0�ђ��|�(v�I6�0�\"KB� �J��rK�`|6����F�T�'�9Y9>r�@y��@�%�ʄ��d�7<��\$p>t�\r\0|�yr�́��k9+����6���#��\"97�� N�ڮ���ͪ��Enp{s^�_;�\"��I�\0�J <w6��e�jc%���8�5�ր�����L&F{�2/w;����&CD���+p��%�#��BYo:d4�#H�!�A�,݃\nsα�8#=g�jl:�U��B�YX\0�eտtmd�(v��@k\\9vQ2��-{&/¶A��<%N����`�EKJ��Pպ,s&���8+-�1�T@W���8�l����D��x76@�\$�v�\"���t�X���vj��@t�H��'Ey@5�ك<ɏ��{��v�OY{LW���r:�(�,̗��\n�+�:(�5䏤�����02�%�D�Q�B��{�x-�(�*�~.����C�J�\n������S���ў#K��|䆮��ɨ2C@��a�B���bCq��y�L�7�K��4���O��fQ=�'���<!ٙ�fP+�`���gND��U���ҡ��!�\$�\$��-�/��3�Az_�@d~Q3��'��>�\n�\0�11�>���J�5���T���k8;���d�Y��^��ƥ���\0�Ӈ���(���F왕���`k���Q�+�I}Z�g0>�0MW{�z_BkП;`�(��-�wJ�e&ؤ;�FA%L\r?!��̋��\"�V�_�5G3���s?-eتQ�,�Y�s?24�~l\$߱eؤ޷�G\r�rH�����A~��O�,�G@l���dϲY���l�bЂ�?���#��:�Sߒ��k�n��ü�,�3Jy�\rg�fπ������v��/�4ݒk��d��A}�OY|t�������K�A���ޗ��?|���ށ-�����&���W`������_�\0S�����������\"��os~���G��r\$�Dr��{#�'���Eͽg���/�?����<������?��:��0�'�����Zn�7���9h@�?����b@(�3�o(�.������,���o>�{���I\"���䑂\"�`9ډ^����-�F7��%��h�Ұ��*֬�@|	\0i����@�@~C��\0�X����X\r,���3��\0����ZT ���6�.<;�C;2b��\0���K=1��#�!��� 5�:T�\nꙪMtᵀ��i�l@����9���S�b�@��(��81���i�A� �@�\r�+�8���K�B�6�~�\r�8-R���L\n�*�`6��1w�B�[�Oٻ�:���t�� A�\n�@�J\"���A8k�l[�������Co��<_�#AF��Xn�l��(��W��,�ꮈZ6���ȭXn\0���J3�Pu������>>��d�!=V�{KGe�c�F龪�Ɍ����m/��0�L��XOi*��˻�\0B/�3z���(��������}�0����+I�BPp\nB����ש���Iui�,�)0���%f	S��h�����Ϝ{���:�P�#�_���'T��k2h� �Ⱦ����i¸B����\r� 0k�ΐOn#>�l�	�\n��B���\n��2����̐�������VOiа��Y��b�s�\0����d�I�ſ	�1�6B�[�,\\���+2��(&��\0���\0�\r��p��^�Z)@<AL�zɐ��U\r�\r���tdH��\rl0D�V1� ��9�d0Lt������@[�5�P	P/��+��<Bz�zn;�f� \"�\n��xg�j���`T�2�4���X� @;���7������\"��ț9h�ۮ��>c<����C������-a\nD\np��9�bZ�����k �����*2�Bʡ���\\1���XC��'��Ɂ����D��D6�; 9;�+Ȯ`���ʃ�J���C������\0002���o���PH�>�\rc�`2A����@F��`ۂ%\$�\"D8����+A�\\`ս��y�&7�4�����x��\0ºt��Ѣp�� i��ZHe�HR�����D#LZ���p)�����.�bɀ,��pB�\$�%xB�&�TɈ`�E(�R��b���\0�;F�1i��o�TⲀ��4/��k<U�*\0�K���\r�Q�Z�e���]\0��ɑLEK����:),X�c(�?N���,W���V�GBʯ�Rqhŀ�ih�<S�oŗ�Y��EM���_�Y�YE��]Q]ų�W�KŻ45qv�����zEB��^�r�4��.���9����\n�al*�+,`�S�U�b/QE���kQ5�Xc��mTP��T�{�`����%�=	P\n\0���x{Hq��B��!R�5�P`��]��	����i�>��¤���h��F�\nN��<<| ��h�Oj��ᝐtڝ��C��)�F��88(�1�8�NR�i����\0߯�����i��蓀-�@'�2!����K@��%X\0����Dk��(Z��\0���\0���룆#���ii������(/-��\$���ػ�`t\$�����[�;^�� ׃���;O/:Θӽ��]\n�Ja��L���9F��RS劣\$�T��d����Ճ~`6��2�	����j��D�2\\OG�Q8����� XE�����4�nl��CfA�\0@�bX	b�Xd��4bk#V\r�t�~�W5�ћFEN`�m���#H��F�OX���\0�8��\$%\n;���(���)���0�\n�:D����@@��)���p	�r����)�0�jM�\n\0�8�\0�(\n��#�!�`���QQ�\r(�8��J5R?��M�(��X�)(�<~Q�G졀Rѹ6�䀑� dmǴ]\"b�����\rȵ��ʁ �&>�A��\$h?��c��(\n�\0�>�	�����}R��~\rhH��{�,G�<�m�(VN��\"�\0_�h�7:،�2A��_�>R\$�1\"\\��27\"z�#�G�l~rDG��m��l��[��I-#Srr@u ;d* I/\"1�����'�]�<���\nH���w�AI �������8#��	[v\0001�^l�#27\\��}��ɒ3#���7E&|�i9����l��&�v���\r��9�'zC./�3'�@�j+�h农�*r@��hY��;'��2~��(96{�A(9��HC�T�D��[�҅�](���,0��u(���}�3Q����)<R�2(RL����\rd�'�\n��F2{J���|�u((SA��ȱ(o%�(� °\0[�.��ʐ3�򙆚��J1(T�2��\"j��ʫ*�7ү�]*���I�:0.!H\n+�C��`����(P?Ҹ���L�aF��+��2�ʀ9�� �+�σ�*A��F�L6��0�\0�+�c�\$@cP?R���# �R��Xy:6p�D�� �,����G�5(�QQԤcP\r��+į�'J�B�8�,�m�8������-��P��pM���x�̥B�V��}�|�G,�< 6\n�\r��ҲJ�S� 9�Z������Ļ2��.��E����1K��8:ՌG*A� �&5-ĸ!jK������Ae-�9�'#/�������U'�s0��'�\n����LUJN.m��Ķ�\nK�04��9Lc��p�\0�<���L0t�2��B\$�<LBL�sLJ�xhs��1l�n'�|���W�d�����Lm,�\"��w*t���Lo-Y�hߤ�\"Z�1�ȥx��焨Ĥ� /�1�U�9̤ʒ�K�2��s.��'(̂�vI���|��������̇.cS\r�\$�����a3�r3\r��J#�i�<\r�� �1�+�΀�J�4\$�N�#���-4j�jM��\n�o/��34t��HʘlȒ��8L�/��4��SN�0�Q���4�ҳRM0]����K����3>%0�')L?*T�s���|�3`̋6���|��R�ͅ3��a�J&�r�M�xs9�2<�s+̅6�(�l͑1�>�9͟5ۉ�T��6<�x\0�\\�slM���/}GJ���\0006M�7j7�;��3��gM�7C����+\"�K�7��s�#~<���ˑ8d�i\"���\$������+��,� ���0�8Y&6��7xb/}#3���\0�8����L��	2��9��Mu9K1*��-/�䲟\n54��q�K��œ��wD栏�o1She�~#��s��l�r��:��ӜN|����\"�4���L79�?O}\0[KӉ�7��eE���(\ra�N)3�ܳJ�.k�2��BF��K���L�)�I2o9�%�|2f����sI�'D̒u��'pSBy���>/|��-\0���s�ʖ�r|�O8�DH-N�<�u�Jm:������=X%)��0�Y3�2��o\nդt	���M�,l�D�ͣ=�K����=�+�ق�6���OU>���I�>\0���MR\n�г�OY'�����A�SOM=D�S�ϫ=��r�;s�sO�=��2��?����N[.D�3�ɣ?���O�=�\0\"LO[?u\0���7@T�4v+p+\$��9L�.��1,H�J̎G����P7��F��5>U���'A5�P?A\\���%?���Y@��M��C4LAh�d���<��P�'�TN�?��4%̢��\r�������oB�E����\nҁ�qA��L��L�a�PDT�	T.��B�\n��Я.��422�؈��)�\r��P�?UT1P�@D���5�4\0��Զ�L9��I�I}'�M��*3\$�`6ɫ'H�rv9��\nP�P�?l���P���<QUC��_QGB����悌P��4���J�2|����q����,}�菦>�0��\$f��`)�PY��(�+\0��0���� �ޕ��bWQ�0�p\0�\ne�\$��rP�s��\n�Q�Q�F��n0(�@#�J@�&ў3\0*��FZ9�\"�����#��>�	�(Q����n�	Fm�h�EF�\n`(�N?r;��\0��\\��R&>��`'\0�x	cꎮ(\n�@��F���&\0���n���\n�Ə��R�/���rD�#�đ(c�Q�G����\n>ďT���FRG�ќ�%	�ѥGxtjѮ�kT��JpAr�GJ�,-�Ү(ԁ#�!e+�H�H�*4�R�K04Ar��>�t�G��R�J}�'Q�G	�rQ�GE0�\0��H���\0�e�F�����6ҍJ�9���Km)�n��P�G��J8t���K�,�R� �.t�SH��T�\0�L�+�n�(�(��1Gu�|��G�\"���H5t����!@>S?M5\"4�R�N�4��H�#`��#Ԑ�I5c�#�I=%4��IIl����?6��RL%0Ԃ�IL�Q����3��S@�(\nT�ұN`0�k���M��\0�I�&�'�qI���T\rI�0N�R��52�r��E7  ��G�, �RoI���{Pe(5Ҋe5�����%�#�>�2`\"�UKe?h��eK\\���\0���	���X*7kTH(�#�ѻKM2�#��	���R\n�%*�-!T�Q�= �UT�?T���1O�\r�.T\\�% ,�UR]K!�Q%+��MQp\ni[\0�J�J�!SQT���^�}4�7���J�T�S5H���MS�O�9�KQ`\\��WS�+\0+%MPa�Q�M`����G�G���?�.���Q㨉@#p*=�'���Rt�Ӭ>���USP�PrR��\$�\0%��U�C��0?�\\�.UuL����(�u7�(�����\0�U�7d�N�If�ME\$5K�?쎃���?�0�j�J\rT@\"�H�x�5oUV�U����W)yS)M�]T���S�\$��p>�Fc������O�Z�U.?�S5mU8%<�(Q�F���uF��V\n�MT���K�_��U@=\\5q�L?\rbus��Y\r4�w�gY!1�#�eX�a@�U�>�d4�\0��\0�#��p	�>\0��=��� � h��?�	��?������L�.՜Ԩ��	@'�nX	5`\$J�4e�K@���V-n�ֱK�u�V�]Wի���D�U�Z���m�6���h�VX[��\rV����M-Dվ��Yui;�uU��)BU�[�\$�ģsTMG4kH�!]uWR}o��H�OoI\$�?Eq��H; �\nT�ԙG�:#�\0���t�TMnc�T�-D�VJ�u�ق�?����T�%vC��ʏeG2;y]hh�\$�W�:)CWs^wuu��V�`�M��^E\\��W�^�*ՙW�R�R��W�V�z�Nן_Jt�א>����׿Wg���V5w�G\0�S�}��F�ZU�V)Zuh���WK�	4��qHU��U7X�hUD��_�y6��F�\\��T�`M�V\n�`}�4�XS݃���e`H\n�G���p���GU&#�%�}r	����e��W\"?=1I�Ze�*֞饄�ܣ�T������,���Xd�t����	�����\0&��kT���bM��P��-T��N`�%�^�BU\0�!����\0�a�<�&��G��H�?�D�%�eM9�=��L��e��}Q6=֤�k@�R\ne(�AWWu�� WB]o��Y']�8��U��@є��VԢ��-L5y��b kH�Wh�\r�VO\0Vj?��UP�Oh�ӫQ�	�#��\rm�W�cb}�\$�Le?4jVk!�Q`'U%^h��R��EN\0Tn휂u\rT��_�*\0�-��\$]�76mٻY��4TmfU&8;p?5RU\"���F�*?�g-��x����4�X쏅IuSRf�i[RSb8	4�ٽg5�6���g�*���Y������b͠V��UE n���6t��}O5��l#�M+�����\"�i5+t�#yV��� �] �QԆ��QM��ZoFե�=Zl魥6'Z�i͇YZgQu����c�U��Q�/5�sZ� �T�0>�&c��U@���Q�!ZM��U��\0�.�\$Y�P8R�?}kiցNM��IT�D��K#�x�'T�RH��7��G卵�Tގ-������p\n�i��Ul�t�U�|�V��V�0�����l����\0���D�[+lݎc�[ ���π�c�M5|\0�l�:�ҤfG6�і\r1�=��m] ���\\�Tm�Qg�1��ہX���᣺>�fu���e����b���k�am �ݣkm�Q�:\0�>���##sn}�'���g�\0�ñ��Z�U���\"�X�uk��T�>�2UR�O �%�\\��b��\$\0�`%7�8[:�����mm�7�mH��\\H=��v�KL�\$�p�KFm\$�SH�Z=���W%c�0�>�c�t���o%���X�}L\0\"��S��%Z�o�7\0#H����w�\n�{�*��i�	n��h?]�����\rq�HT`�V��meU�ꀿK�i#��v�	 \"\0��Ű��#�PM�7�Ih��ԝ��\n?�g���T7PEAT�R�PrM5`S\n5x�����@69�h�E!�6��x�T�Z4����\r;Qr��(��-K�;���` �t��UK�/V���N@��S��� �PV�m@���n��v���bT����t>�E5�;jC�?#rLc�����T�[` �yT���\0�p-�W3��������8�-I��S+T���]\"����:�������:�=�N���)XOo�:�9\0��q6�ݯr��@!��� Waۑ]e#@/��?�2tT]wU�v%�mܒQ�'����o\\շ֑��H<�4�\\Yx�SaYU\$�0XqHŔ�Sb�� W)!� �>Yyb-�\0>UY�K�G\0�k�wדSEy-�n�ck-�	؟P@��\0���WY`�\rgt��UD����1=��M޳!u�<Ħ�C�ר\$t`d�9���́\0��z}�cJD�@b�;��\$.�{���i���TP#����\\ɑ���ȍxT������k��|&e�<<D,��B'|8W�B�zk�-�^�p!�P��f�%:�\r�\r.\\_1z�\r��\$�=�0��G|�B��Ţ��{z|Շ#='����ڭ�*Rź�}��.�_nF��7�C�}k�P�1��0��ZJ���/�_eJ� 7��� <�n?-!X],\n`+UQy]�6�Tr�8�UfӏNM��DR�O�0�&ӑm=��5����i6׍]�;@�=K����Tj]�5Y�����Y]�\rwh�ԑRP0����]u�2Ӏ#��_��iG�*?�	\n_�Q�n�̔}4�0�m �0�\0�t��*:� �,��7.�;��� ���UX��*\0004��9e�.���� J�	%\nM�X��>;�!�Bz@���MtHa>�1[��?\0�N\\�<,�+�ЖAv8�D	D�v\r�(���u�jƔ2(�܃n�Ij�H\$���/^�!s�@�a\nv�&d���/A��{l�N�Ơ`�'���T�n�,!<k�:݄�S@��]�c�`،hT�T`�^ T�?;{�p5x4Dx=XkA����\n�A�� M��������\$�S� �N�ìo&������� ȕ�:��k��N�[��	��n���ҙB����߮�/�H����z����:�,t0+��2;�����a)��vPL�z)	{��#�ڂ��6������3b/�}��;)��� *��Qb,�p�b&5�p��P�ΕY���1��\rX\r!%a����<�O\$h����\0006/o�i{�)����[���*��'�4G��p�a!Vh@-��b�H?� ���Jx����Jc-��>*���f��b�&���A_��\"�%��-��=�W{�J�Yb�~%��;���%X/ ���\$�Qb��G8����f,����\rx�c(\ra��:�v1`>c��&a�����a%b@�qL�HkW����t\n���	����7�ɤ�+V|���?���N��cQ`� cg�h 6����F0�86xߝ��A]�9\0�88��J����Ճc���η�1@ 0���ab��7x�\$?8�2�NS�\$�J'D�\\�5��A%�1�v3��O�3�!7N��rh�#�;7�����{��&%��Aw\$�:���;��������pK8�c��5�ܘL���n,Ȕ�Ȁ��#����	�\0��@:�R�NEB�3˯���.h�S�=�.3�\"��ELs�cR�v)��ǭ�\$�����i�O��FImљn��!���Jb�\r�T��d�|`O����n�;(h�5���w�d�;�kN�ʪ��73�T-��78�\n�UY7D���s�7@�\n�5.���	Tsf~�k�n��)	�mA7B��N��d�ͦ�>@E��&�P@� �ツb�ҝ�:��Ҝ�AE\0�<\"�Q�k�������7X����:\0��at�l��;\r�q\0���)��|\\S;(���Y��s��_^�c��&(�|Yj^��~Z�DƸ�K���+�\0܄��;�=�ї +A�(�6\\i�Bz2mXB_��}�6߉.}���_���ӛe� [�B2e�|�(��fz�Z�����c��f}�ن\0�P@2Ad��by�f��bY�Nm��A�2×��d93f\rvd����e9���dY�f�na���c��e���/��fٓf9��f�e�~4?��_{����f�-�l�~7ں�}�bY��vM���LL������v����eш\n9E����u�U�Y\\���	�#�\$��n�g�B�<� �~����w�\r�uC�����W-d|��Ǭ��y���Tz�	1�,k�9�Q�VpRO��,hCB���~�nY˸Q��p�j��Y#��NX��Wum��Z�(��g3V��L�^oy�gq�!�gz!]�p.:�q�)	��gtJa|��u�܃�a6	�/燃���4d\$�6\n����2#1.g���s�ž���\\�&u����+�,g������wy�Y�K1�� 0�9��:מۭf6�˞�xY�9��Qb�\$��~tX'���6z���.�m�`�1�9s�@4�̓hD��y2�☾vqζ�VD.�\0�6��<���\"\0�綊k���>P9�1�vzϏ�\r����N՟�FY���V}\$:���6��`��::';�O�Od\$yF~��8���\"�턚.�5y�6O�����,Q�!=�t%��e���\0�\0yf6��}���R\n�A�`�P�r,�C\0���k@��S�zB�QCX!�I\0�.v�N����\$��@�Tc�F��Hi�Z�2֑K�\n������)]��i>�77�߀MbŸ��?����ŽC;�C���ޓc��I��4������#�0�hT�M��D=zM��X����CY�i�@`�,����y�Cݑ�i��c;�zV%������,M������%~�:ENY����.��NY�N����/�N��7h�<�A j�\\\n�aW-x`ډ��d���i~KP0�M��*i��\$�Fz|�QAV�I�=�j!�,:tB0�-�z����N���V?@K��AzxDb�V��K\0��8KD�����^��;��Gg�je�Ý�F|��oC9����u��n��(��\0���*4�A1�����j�\n��B�f�=n����Q���zxb܂D47i,!v�JP�!�XΎ��xP�{�Zv��U�Ӏj�B^!dj�\r��������K:4��z��4��bp�l����C�Cܢy����Ao\$��)6�z��Q��?A\r`���\\zEיִ\r�݃s���:Eh�e�>�Ќn�f�nڥ;����B��管��j�n~����w�Tho��M�[(�KKɮ���t!���ˤTx�4���o��y�Ɲ�EKR�6:KG��#�.\$t&��7c��-���@�]�Q�Q:ʊ߾�Ҩi-�,lQné��qO�+G�H�:�f�:�ꓯ�ID��_��Bo��M��Aj9���\n�W�3���F��~�/���f9	�0>����G��d����D��\\�A��]bK�\"\r��F~���[��c�\r�˸BOs�1�d!�y/Ѕ��n���\r�0�7�\r���	�%����h\n�2�l����Jב��ց8\"� h�Bh��j�J7�-b*�K�����!�FCV4��SK�ًF-����~�2�;�F�KÛ4������n�Z��1�vR9��\"L��:.�ν�dQh����k�a�n�k#9N�9��Ʋd��U��\0N��6�O��V��5+�iǢd��]{ج�����c	��g�AM^=����U�{vl�\$�P��5��/�(�\r):`F_:Ɨ��=�	�!y�V��9�ϟE�Q��5�>���:5�<c����Ɠ���z���	�M1�[�n��dn/����F�9�F�#`��v�X�<B�Fj�dN`Q�5�󞾴�K��5o���	�h;�������#���BZ�>����o@ck*��@����֓���D\\�S��)��pۭ���sC���6��pU[��G4�����?�.�e\na	��>W@��{�.��£��훭̵�\\9ژ>���CA�����ץ�`�0���d�]�f��M�1���I7�[����\n�]��,�q�VJ���ۑ?�tz��]����um*�p�+틽���.���\0H��W���;+���Bzo���x;^nE�tK��hq�����ꟓ�E!�+n=��T��瓗��xkj�6�{������#�h��#�[�o}��q���P�DղÝ��������o�1��xc��8D�\0�񲆜�J	������v=�W�Fzz�mk���hOޓ5j\$��X��}�<A>�n�{~h]��\"�\r��GD��x�Q�)=:�5����G:�P��D8�p	�sH2pzt�������\\ڀ����k�|)�Yt	���P�E\\D�0����¾�|p�1�Ɛs=&��`�h���IO��\n�,�M틂>Ae\\}���\\>�գ�G��7�N��l\\��L4!�5c,�T������!p}Ĭ��<�Q�H艞�89����!=�F�1j��ː�A�@��o�6�ۏ�U���9�������Ĺ���q���\nM��<_�}����3q��\0���\$n��o�>\$�z/	��+��q}����1�o\0�F8�?��P�����r�������;<�NG���E�c��\$*��qU����}��s�F�����8��b�C6��\rk��G�m� 4K<~4H!��j��m8Nkr	f.U����z��h�#�S�rU(	Zs���n�z!�/%\0����/&�}����ں6rxW`5�cG���O��b�W\$�b�M]��\$�?��z���\rޭ\"q�����J��Θn�ـ�A���&}���#[%�ɸ-�'gt\$ƕ�j��L�wN�re�\0\$8Z�#��:;�s\0M��\\������s\n�D�M�eA�������f��4I�BԾ��p`��@%Z�\0004�0�}�O.�\"���L4����]\"�'��H���f�י1��n�ыRet�Fޮ�.MY6���ȏ�lc>h�5�ӂ}<�Ɍ���(��7FL�r��m2(�%����b7��C\0[͸�M�s��#V�6�Χ5M	&v�79��7�����@�!�\0�|�N6\$ݔ��v���n�!�T�Ƞ���<��WD�@M؀_�(;���'h���L�d���+��r��Q�ˤHi�ʱ3,�)t]+��p=<�tq1o3	F���e�����}�%\0001R�,��S�O�_Iͥҍ)lt�8�LI�t�:&��\0�Ҥ�!?�_�^}0d�\0i\r'��g�A��)4�?���/Lt���θI�E�|���4W�?mi7���g�	Уu��/��C1�I��yI?C��{SZM�e�m�K��P \0��~�\0��A5�#�.\$s��Y)���|�ҊM9yd]ϫA =9	�h�^���rE@SO�#>0L�HK��HE�%t��.�m��O���f�ѸR{�~��F�%�8�sK�B���Y�w�]/#�Q����cc�)HT_GX\\�p�r>�Օ���F���lX�c�V�nu�����@u�d85��lB� �-hE����TV\0�h�=`-Tuv�rTg^5��Q��=b4l��ZMU�Yx�u��'vC^M�c�ٓUES��U1#�d�&v�en@�R�n%�����?d�_vOeŗW��iT�wf[)�?a=��_/iVM�X��]��Vod���eڏf���EI'j�,���mp��Rcj͍�8�?^����V�g5�Z�c�+}��sk�\n�W��ueV�Z�۽�v�����TlU�^UU����[�S=÷kٝ\\ݛ�;W7guxҿU�8�6����v�v��(�v�U��Os��է۽ow_U�?�i�Y׳\\utyQ���u��VM�^]��ck�n���W5e��YG^�%��]P�_�[cW�s�|V�o=���X�wu��Y�\$ݕX�Yq:w��]f�����d=��CU�d=�v���=�Va�]�H����`\n]�w�?wi���QlOj����z��g���u��I����{Y�x4�ViH���FVl���+�{F�Õ���>����\\�sErVrܟ��wY�}\\u���u��Ů�y��d<�c��p��t�q]9]��!j=Uc;yb��GS�RE�הT��?s�'ׇQ̅T�wF�}=��Um����w��-6����S�C.a��g&x{����-;�߁�i^1��|\0�u	Z^(I7�������c�;V���U%h͜��Y�g\r��t\0Qh��v9�cP����H�y������?8axD��g�-�!�3Y�g�\$��Y�ݯj7��P>���ee�Xb���s��h�a���Y�D/f��n����n�=�	^μ�ﳞ:���V��[�L���N�a����x+������w�9/x�>�+���a\$��L;(���SF�t����o�;��ly��xs�\"�	E�����ߍ�-��@׿�5��>��~=�!�\0�1B�US�b���\0O�8L}��ѫ��4q�8L:��.�6��3�.�Yr�oɀ��Yz[���_+�Q�p��?���62�/x�b�2ځ����~-0+���r~�mC�X!��b���\0����A8�9��&Rh�	H?ɖ���^��W��d���E梾�bϟ���z?���\\<j.� Jc;��\$�)�;N[�����yj	_��H�I���:�B*���ļ��3�:S�������.lf�P�Qö�hF[����6Ý@p\r{����ӝ�e����;|���V�s��FN��P+��k��o�g��̝6�[���>����֘�{l�+7�{��+�f����\n���cl=y����py;��B��\n�������ìm��ǒ��y��%�h�@�L4``�{�cnF��{��k��z���^�������[��O�U|\0�����.�d��w�y(�g�nJ��d�ϼ�AOQ�F_:�b�PP�h����a����,�	1������:']P���g�}�6��6XЗ�Ř/P��/-�I���>�M��x1�b޷� �U�#`��d3����z�Ŕ?�6�C�tx���ǻ��:L���׻�#,��?0|���S�mw��T��i���6����8���/˰�%��*h���wç���,��@�`���2���M}���E����� �%�o�a)�_���Q�NM�׿�\"�Yά�)�������P�w�RMƇ�?ա.B\r�5�TbX��\$X/t���!)�	)�I7�Ľ[1}�n��`�����o��`��~�AΪbt�oʒ�wڟh���n�/{Iԟ��}<v� �b���(>8����	�\r3���\"���(\rp��\r7ޟ{l���:������o�^.}��~ݯ����/�.m�7�\0s?T~?����><�|��o�M�N�:Ơ�yJq�\0��o�\r�,<�}2	PJ�L~?;W�-�i�_ݼ\\}���:\"�PA��;5�������\r�� @���+�8�~��fDߤr\r��ٟ���,t_\"����ƿY���?����'ߣ��������}�cٯ4�\"�l]ef��Ȑy�����[�I�L��N����a2�����!f�P����S��#	4��_���J��?�߽��Ġ���[��~����EN箒4*ÂU�\0%���8ʇ�Q�`��S�����H??�h\\��@�P2 J[xL�G�?�����\0�ȁ�>ü��/�R�\"3��HB{����<�.~܄l}}�<�|����_�^��w�/_J�:�ަ�&����w����h����k�lN[�T��@(�z�~M�0�#�h+ܓ6GETh�ck�ѝ tS2�(�q�[ŠZ��_�>��Y\n�TTE\r\";(�X�s�������-��@�D k�S�J{(�p��� �a���^\0��bZf{���#di�����D�L<��2�l�Ĉ_��v��P擯�	�\0%�S���0��*D��!ֽgЅ;��v4dP'1���q�ZXb.Y�f���մ[<�c��S����['�+����Ђ|^�p����� �V�b���n�1(p��\n\0�2�*ge G}� �-/;��1^��\n��tqz��P��[� �	����p\"%�Z\0d���\"�9�+��.FO�L1�o}�jO����P�hCDE\\d_j��9L�c&��9��xV�7�5��|te�16�P5B��\0�}*�2J�n�=f���BQ�'�rR	}���RɎB�8>�K�ưMC>Qɪ`P3inկ�wP���a��	#�c�3��Y�H���E�h1��_���k0\n��pe�Gǟ�1eh�=\n29t*���\0h(���!sQV��\0�{j&���+@D��[ַ0ul�a�#��M;\r�tXǁ��j��hQε4�CM�3S�M_w6�;A0n{l֠�Xx��z	�zf�HB�rl	K!dO�# n~��ps]�.1��jh�0�!!r�0���p�p�d�9iD�%r�������f��\0�P4	3���g��7���>J�\r�L�M����2k���+�8*��Z��h����Fߌ�ґ1Z����hdFٌ.�A�й. mNY\0փ�K��X��Ax�6Q|��h8f��c�/��%�}��帠q�c�nWA`���`PB�L����惁ɂj`+����\\f����;������g�ݘ,<�C���;>g���S��:��8�\n,�۳�XA���	c}H?ò��S=*��8@���7R�(���č�^ˁ�7�gj��߀W�8�z�8�Y��|Cܰ�A��FD�}�#PxE\n#8�P��5�n�M��FX�� ���6��r�ݟ�O�z�B_`L�Ԑ���bE��NM�Zȁ�������\nP>Am���7�PG��Gx�9��1���\09B^kt��97�P<7�V�q���JN)_u-�d�a���G`�<�o�ĳ\$'�JM�����M�	�yp�܍B4��i��(��@�8Uhb~�<(�\"�Y��w4�X�7fzPA \"�ā�A�b��T�Tm�T!����9�.�PB�L��h.�U�M�_ĕ#Vp���B�(�����[e^	zG-� �9g�tE�d�?�C� 2����V�ɈSO�'<Z�u��(�ҍ{��e�=��C������\0����v�p�O&��Ki���� Cಷ4n�|�,/�'MP�U��~�lxv����(֛�(NQP۰d��\\�TsΑ�ڨȢ���ˀ@\0HN�\$x��No_�)wYx�q�<8��\\�9�sN͖���'�HC\"����b !��RIN�� \"KG8��	�\$�s��K�D�F�!�������&���i �@�b7�;h�C��{��H��Q(�=�5q�0�TO��K��4+{pO��%\n��	m>JW�l�CR��r��\$5)�V�Lp��� JE\r��ؐԤ�B�8�i\\��6���nb���&�\r�2<8�����m�ۇ%\$ࣧ�_f�!��_7�\r�+�63��������pǴ:V��#�d'��d�M�t9�j��J#CYr䔾L:�u��~�=�:t!��)A]i��f�%���Up)V�.�J9nyGn�n�{�ȇ�����W�\n�U�;�w���^���G*��\n��\$ޣ�Lr�g�i�xdt�e:��b�ݎ�>\0��K�u%�S��*�x���ݫ�7^� ^%)�V\\��Lb��r�T��6T\$��M\n��D�<�,cS죉L�A?Ka�DT2�� �@�!���.U\$�}#ۮ���UT.6v��j�巎��C��vⵍp�֕WK[	��\\������'p.ߖ�;�Zb��iR����KV�-�_��i���n����Q����#�}�nU|��Z���frG������]��v˶Հ�����U[�Yoj��8��V�*�w\"��y*�E�+YH��Z��9R����e�� p#��aZ8}Ek���+�xh�Mx1��L'P	�:v��_��e��Aփ�u=Qx�@h�+�ܝ�\\���I\"�\$�n��C&\0��t��4@b p[��\"��K��D�V��MM���K����Y�^A�?d)�X�!lI�D�k~����?���K�g7�\n�F� �(��,�,��l��9���'�Q8��DoX ���j`մ����h���r���y��M�n\0�<���ǵsF�6�;Bug������s׶�\0yl|�2���\r]�s��j�2B+у��=���p �DO~���2�++���!^�H{���_���li\\ˆ��`\n�K�&�/���j 9�����ݢ�cd���D'��o@���cD�/?P�\n.Y����\r�%�\0����(�LED�G������әҹ|�x�kA�!Ic�4Aeo��q� '�9X���Xx�CsW���ґ\"{�Ӏ\rY!����u��)��\"5fFN����E���P������H���H��l	&���Ӭ\"�m�Q�tZ�ʑW�+Ų���\$ ���.Ǌ-`a	��F8�o��X�#���ឺ�&R��>��> ��}�\\���X�9v~��.�����o�/#�x����S�,����4���c>��pC4�����hg��\rE�1@O|4(e�\\���6*��	��d�!�ҋ�x�Mp`\0007�D��4)cd��P��ZV\n�ɸ)���@\0001\0n���a��\0�4\0g��a�\0����5���P@\r�F\0l\0��XƱ���#�w��xƥ���,��\0��dƱ�@FH��\0�1dd(���8��Zx����@F.:�1Xh�ш��6\0a�2�a�@\rӂ�`\0g�2\\a���c(F7�w���ep�c�5���3Lb��Q���7\0sV2\\b`1�cF8\0d\0�2<e����F\0aB4\$b`эM [\0l\0�3�f8����Z�:��hXȱ��OF���4��ɑ��F�\0ir5�e��Q�@\0001\0m�0�i��q��`�+���g���@\0005�20�k��Q��PF;\0o�4dk \0\rcbFna��3|kH�Q�ciF0�{�1�e��#(Fj��|�\"�q��Fe�pdj7�d��q��GF��7�nh�Q��9���B2\\k�1�#OF���M>3Lj����5���\0�5�g��q��=�݌T\0�2�g�1ǣ(FP�!�5Hh�ѯ#^��<\0�1\$p�@\r@�Fb�I�8�c���cF�����Hۑ��C�G���1H�Ѻ�\r��\0i.2;��Q�clƂ�I^9Td�� \r�FFe���2\$b��q��7�[��f8\\l�ߑ��qG����e��񇣧����3,exő��GA���oX� \rc�F��P�a�Ϡ#�Ƅ���5<q Q���F����6�l��ѡc�H���<,h`��ck�2�/��g������a����d�ȱ�c��;�q�3�l������F8�j44{��q�c8�O���<�c�����-Ƈ�~8�s�ь��F1��F�8lf���iǌ9��2lx�q�c��]\0g8�a����ʀ5���3�l��Q��G\$A�?m��q����L�NZz6�u���c=�܍G68�sı���G���@D~0Q�XfGs��=|g��q�\$G}�oz?d��C��F��SF6�o���c��<�9*9�hh���vGG�]�4�e���\0001G�\0c�3�Yэ�H.�!9����q�IH=�U�;�hb�Qˣ�G�W�A�q(����\\�� �B,s(���\$Ɓ����l��qҤ�Y�]�2�x��/�%��д�p���a���M��&7�m��1��G��NB�t����&�֏��4<e1�#��O���8���Q�OF��CR9�{�1�dF~��25����c��,�E�=Ll��Q���+�E\"�2�|�ȱ�#��A�G��I�a��HČ��D�dXб�c��ƍc=4���Q�cL�3�= �9Tj���#*�C��\"�F�fx�ѡ#3G#��\"?���͑��VG��#28�}X��c�B���;fy��#1GZ�e�2\$�����^��{�9����c(G���C�oH�Dc&�7�3R=b9�ң�Hy���=�x��#v�@�O R:�|�Ѳ#&�\$�\"�3܅���L#�F���#�3L��ñ��,G�/�3e�Nc=ȭ�I v4,q(�1��%HБ�*F<|��1�c�IQ���?�l��Q��.I��\$3<��\ncvGu��\"*G��Y�������<Ԍ(ױ�dG����J�(���YFS���A\$��1�d��S�5#�6ܒH���(Ix�\"Z8�q���#\$ǯ�; Z6Lt��ģ�GJ\0e\$�34n��1���I��\"�G�Hݑ�#^�q�Y�3|bY3��#nH-<�>�i��1�#��ג��F�Y\0Q��FFD��Md����c?H��LJB�bI�T�3��I�@T|(�U�5��\0bLJBs	4��>ǌ�m:�b@r ��HA�W1̇��pc��ˑu'�BTa�.��#3Gz�W�4��Ĳ��G���#>>�u��4���&�?\\����dF����K���#�c�I2�K�J}�r`��Ɉ�#�=�bi?q�#5�m��(^:k�#R6dVI���'3<yҒ_�.G4��&�:x��2G\$�G�{r:�p���Z��Hm��v?�c9Cq�c\"H���!v3�w�q�\$�H���(�KL�Y	�3#�4���?�1)\$���ǣ��'�7k�*d\nH��Wr2�X�����#�E�x�23e!�k�(b98�8���<��v44u�뒓�A���*6O����I%G���H�	<���GI���'RKl�h���c��W�)<d�	?�i�Rǌ��%�L�1)Kq��Z�b�?fGtz9R�c��Г�F=�}�RK�nI�I!F?<��Gq��j�~��%\"3Č(�;�\$Jō�>0�9*�3��I؍e\"&St�(Ų#��ܓM!6Bԡ�01УYH���VAtp�Z���]ʤ�w&\"G���2�jG��#�5�k�Nҥd�ƹ� X	,��`Rd�GC�3\"�;�z�O2�#b�\r�'�>�m����kI�_'�1<9��1xc��\\��t�\"�%j�V,�Σb�C���@')�\n�g��V���݇�\$ڻQJ�͉hk\rU�*�`M-�<�EdBc���MUU-<B��i���Y�(w���ؚ�娋Ge��o���J�ŕ����^��B���Q�KZ��\"[���b��^>(�Y`��LM?%�?% -f����T��Z<��[��p Ľ�]v�-�J��mr�ѫ�v�-an��` �,�p����qs��:��%���P���א��Wb\0���h��G�c��%�˷%|���z��0Gސ�ya�)4�p#����\n�T�O0}�2��/p?������e�;�W�&0�ĶE^�nT�3�z��c[��v��%�<���]Q4A�}��ԁ��V���T�}�R<.\$�4�쿷����Fܗ#0N�������Y�\ri��\0kGZI�k\$�k��Nm�s\n���5�!KB%�K``\0����'��\n}��D��f����\0֢<,���-�@��ǍiK�_�,�f�e�/�����Z�u�`���S��0�jX5@�W�D���Qgp��\nubZ��x=-\"a:�\0J��\$��x�1m`�� \\��@!-Z��HJ��)Ց�	4M\n�e���k��e�5zb��|@�P0�9ZF��f\0��\n��/�=˞����dR�����C�K��-at��l�J-iT\0GD��U��Ƭ�\n�]Gjŕ�\n;fGKW�!2�eX}��j�%�L��_2�\$����+c&U+�X���d\nƕ�\n�_��\$N�]\$��0��%�z��-�^2��s�\0VIK�Y\$�D?Iv��?Lt,���ε�R��U�mJ�f\\(�P#�֖L�\$c�w�j��g<~bPi>Գ\$s �<<�fg�%~�p�Z���f��@kKʁ�,%Q�0d,M���T�\0(^j�vh�ϐ*ȘVJ�WY�\"hB&�k���)�v������.�]��YC-�g��U\\�C�\$��4�]d�Yu�%�W�+w&��>��[����M7v�-sR�)��K�\$0��4��Z�ɇ˴\"��S�8�!P\n@!��\0���tD�We��#)Kv��e[��E����<�C��1�j����\n5�MW�O.�k9#�����)YR.fk4��+D�f/��3Fl��+*���lR�6%�Z�E23	��i� ��l�Їr�f��͙%�-a��y��Z��MqQ��j^���seՍ�Κ�-Ze���k���x	5��s��{�c温v`1�^�Թ�J�WL��x��2^�%�A��̳RZ��]����_UĪ�^V��MY������_���k�Y�+�UUMj�m7)ZB��uZ�D�m�6��:��j�xf��`�7�d���`\n�M,�H��Y��9�J���[�ȯfm�ܥ��r��M}����X����F�{��W��	L�&	���f�;Η�ﰍ�q1�L���8q!T�U&O�����2Zo���d ����\\5qb�9�'�����ef���JV�N'w�a��)Ug�V��M^����r3�٭�f��y��.�?t�����Ms��Jk���k�&�-���5�\$�\0\n�f����8�m,�ɱ�&�;�~w���l��9��Q'3͞%6mO���������E:	Xz���'3�A�]:![��I�3���\$Xֲ����Ն�nW�ͻT�:A`��a���MM^V,��s<܉ԫCf�<K��2�]t�)�Ө&ꉌ���Qed�wa3��UN���:�o�	���� ��ҪRo4���s�'.;��^��s:��fӴf��m��:Rvt���s��c\0Y�.�s:�8{���PN���8.v��Y���'��V�0�u&�˯g<2�x�q�����z;Y�h6i�����Nf�NM�y1wº�b3\\��4RQ8�r4�ߪ�*KG����<�5��S�c;Y��5br\\���˖g(OU)9Nx�����À*�윳;-e�wjS��0NI��4��:���S�g9���<	T��I�K�>O)S�Bl��y��ѝ�Nx���nt3\09�\n�O<~v{6�y2����՝�M��=UT�i��<'�Y�|�Va<�Y������I=�X��b��'�Ϊ��7-e���S��\0N�\0�=�u��uT�W�����!Qt�i�ve��R��=�q,���s�g�+\"��<�o<�Y��yg�Nʚ�9nt����s��Nϖ��&t��y���%�Cʈ)=M��Wd����+;�[i��gy;����&p��M,\nC���G����D��Ӭ�U%?(�:�ũ��(\0_V��H?x�	3�'G�ќS<I���}*G��K�T³6Y���	��g�KF�J:z�C�ʤ�+O�~��O��9r�\0&�&y\r�Z��/*�f�K1<���0�+��W,˖`H�- ��Y�j_g��H�,�*��dr��-����B[�ө�M��_#p��<�Eɓ�ԥ�\nT�@�%�g*�@)�·�.]��j�hP\$\$��S����%�͟Y�8����h ��i,ʂz�%'�����I\0��(r�gF�W��]aYiE\0�)q֣�Q����wDD�|3I)�[,�ft�P�S�(7;/H��uY��ݢ��9�l��:3�ԯ�T����*��P�_�A�b�:���!O֡BQj�����Zg�Nc#�Bl��ZS�V�О�>G�ʦ��'gP��IB�qtCJS��W)ZҐq!Tղ8�Yg5�o���^�#uA*��eNk�Un����-�,Ь��C.�E04(R�=���y ��q�4����L��O�\r���m(��W�?YRB��}�8�\"�k��C*�`���hnP�]�C*lb�9kR�*�'C�b����?�Q\0X�����gt%�vLX������TDf,H�ȵ�r��*�?h_�!�1Dvl��9Ћ(��D�)C�zr�)�tI���X��>��'���.S��J6a�&k29�u�د�S��5�b&,�#YPa*�i4RrP�YE5e��KJV<-QT�EM�H��Zh�)|`\$!kH}J*�d(0�;X,���,�E�Q�����}A>�奡�ԃLCS5?��:��/.�h�,^Tn��i��Z/�y,,��1Ew���a!���Q�\0�Ef�9��I4��1]��˪S��ћ��Dq���碼:�,��\$�Vm�d�&6p��jf����[Q@iKZ�e����ͼ^BB�\n ֒���wG߼�@�Tf����<�`-E�*\\X*\0\$�Gv��* �t���]��Hp�z0�h?�{Vv�u����q-fH�N]�r��ȴL*���#\\�BJ��U&4	��O�]������@�J�0\rTu=b�ʣu�*�N����<���\"EF�V\\ϛ�\n@�E�/W�R��\rh�I\"� �qb?�T(�YZB�Ջ2Ω�N������B;�{�kL�W�Je��JFJ�Ծ)���H�JҺ�G���h��Qr%ZS/�+�1����bʛ\n64wh��ݢ\n�_�%��='��'vI�~r���S�i/.��ĩab��E��@ ��-��Y�2��?Q螰��RZ�U�J��R^:�3`�K�U����ѐT�H���jQ?��f\0���RXY�jl'Y�~�,�Y}�Z\n�(�R��8��Y�)��Td�\0�Q��s�@�H\n��\"-DT�J�J��JU4|?O�\\]IyS�����U�Ƣe;���ɩ\nh��-�[��ʖ(�!��&�/'�6JV�j��Vk4gخHv��Q�#I��(Ι�:�}�%u1Dy	��n�������ԙ�~Ҝ�7Jf�*����1>�G\\\r�!�t��R��K�Qe/�4�YXRo\0P�p(*��)�\"�#����\$S��i�����)ra�\\���/(O�\$jF3f挀��(t\0�`��d�U	>h��e�c����H�\rp�`gP��c�[=�L�f������\0002\0/\0��5\0b�!�`�&\0]*px)�g�CZu�d-�<�\$���k%A��z��d����Ҿ�Y\0֛5�k��\0006��Қ�c@�@\r��x����7Zn4�@�z��M�5�\no��i�STtF5\"�U8@Ɣ�d�]��M��]5�pg�)��b�Mڜ\\�r`����n�/M7Js��)�Sy�GN*��7�t��\$ρ\$�6ʜ�ju��i�ӍsN&�d�Zvt�#PF��vҝ�;jq���G��}N���<jxT�)�SƦ�Nޞ%<�\$�\r)߀_��M��E;\nx���S�����=�ot��S�!O�U>jx��i��G�O2��tzyt�)�Ӓ��Mf:==J~��#��ا�NR:==�xt��S���PP�@ju���F~�eP0�@�~t�i�Ff��O���Z�t��SϨM?���*S��?NB��@*~	)�Sը-O��=:����jӔ�oN\n���z}��*Gq�]N�̕J��j!�*�yMf0�Az���*\$���Q&��B������-�wPR���ډ��\$��Q��M�ImB*�ui�T�[Q��5�%��j1�j�Q�BEZ}�*�#��P���E�5*!Tt�MQJ��k�Q��A�0�\0�C�CJ���T8��Q�K�G(˵\$�=Sސ#RJ��E���*ITl� \"�5I��0j+�ޑ�Qn�D�����`�b�#��G�ƒ0j4T��[PBF\rJ�U(�8Ը��R>��kڔ�+\$���-%'~�5Gژ/*\"T��S:��J*��.�[F���&~��L:r`�%T���R���Mʕu6��Ӗ��Sr��=ꛕ-i��ܩ�\0�3�A�m�<c3��K�QR�uOJ�5=*4T���Sҥ�OJ�I��T���Sƣ�Pz��)�U��T\"�uPDő��{%��[TV�-HJ�5B���S§�M��uE�H�#�RN�5Q8��F*�U�AT���R��Hj�!�;T���0\r1�cCF�9�S�2��Z�Q�cƿ�WS�5R��ruj��sU4\rܭJ�\0�}US��UZ3}U���E�*�X�?S�<\rUh�5X�	�`�S�:UV*�5E�[�b��T2MV*��=�?UV�eUګ=Qhĵ]����	Uګ}Q8��]�����UZ:�X���=�OՈ�]S�1�X��ua���K�oV\"�-PX��U�6U��TZ2�Yʱ�=�k՜�'Vj��T��ug*����UZ0�[\n��Acհ�GTZ1%[\n��k���Q�eV­mT��UU�E�īgTN4=\\J��Ac��īwW\n��UH�q*��UL	%Et�r�*�T��R��	%\\�Œ�*��ϫ�֮�St�@I*ꁻC\rR:�R_�0�v������W��E]�����TͪW֦}_��UF�U�9W��lb��U�j��%�W��E`���J��U�'Wb6ma��Յ���D�1X&N�_��}d���a�X���^��5�����YX��Ub��Xk\n�)��X֚�]��Վ��U���X��cjõ\\kGO��Uֱ�sJ��}��GX��mbڼ_�\$�l\0F�|��Euv��B��2��k\n˕��/����%���c�u�*��8�Y��e^걵��(Vr��WZ4�]��5��5Vx�AVn��Y��5�c�V)����ZZ�5�cV�WZ�q��mkGۭ'Yޯtc��u�j��8��Zz�%4���kQ֘��Z2��i��4�*�ɟ�	&~�2c93��CQFۭ[2��]3��@�ɟ�WP.��kڡ5����Z�[W�j*���d�V���YK�k����*kV̬W�1eھ��k��LYv����˵�#.�ΌgZ�3]n�ׁ����֖T b��<�jKl>+a�+<�������]��7\0�>��<���s�+�����=LR�5S���+�]��\rH2�Ȅ���������[:�%cs��*O�v��iL<�5V@ב�w��C��2�5�S�'���v��|�i�\0����^�n{��j�f�V��J��Hd�uBs��;W��8��Y�t�'���y6x�B�Dʤ'WN��|�ً.���P-�q@	���fsr��͟��8��*����í�`1C�=o��5��Y�%\r5idE���&ѻ8�B���\$�5m������:k�+iƋI&�W\r�,Jڸ�q%<��k���\"�!�q��U�f�� ��^\n�rY�U�V^��#�6�Uz9�S�+�)��W7*eD�)�f��2v�\\�f:����)��O����n3� \n�k���\$�;�p�i��Ք��ˮ�8�+����*���\n�ث�}��9�Q��S��zh\"���Uا��B�o;r��i����pP�w�]�vj�U���k�L�ҥ�������ߧ;;�eL�pz��U�����&�]ed5��W�0���=������U�F��@}W�ә��\$;��^�uZ�:�t����j������݇���&��(��\\�?�{��\rk�NН�_p��S�UX�͝�_>�=�'�ik�P�w�;��Ey:j�a񬮉Y0A�E\n浾r�����h��5�3h�W�w�\ri:�i�+��R@�F�Vx|�W�1^Njx\\I�f��Gpa�,�T��N|z��cs0�EL�xm5*�5��ά@Dg(�����Q���������\0\"H�X�:�Ɍ����4�\"1u(.���`Ӂ屑y,0��R�`��5�A�-	�~v	�+�X�qM��s��;�[0�Bf��((&h�q�_�Fڃ�8����~6Ob��8'�	���X�dfu`��42\nؿ|.Ku�P�H3:^/�G|<�Y�(��<�\n�b�>�	�Z;'z�c�\r�3b2\r�N��L���2�xU:\r6X��-�b\0�t��T�%P������X�SX!�k���ćQ�u�a�?�v�g�.���S�S:l����d�t��H�\0=/�`_3��m�F���%�l�bуB0 ڦ�k��5�ň�(�PO?�?Ί�<��\nЋS�=5j�\n��{*\0��3��b!eT��F���3����<bʃ*�	���5F�c�	�N�	H��=�ga6�e�\r�� �6��;\0�&Ě��a��Qe�4�Ђ��h��YadL�	\n�դl�*�G_��ׅ�	y��H�1�e.X�j��tY�2Mw�4�6�J�]��MȐ�Ͻș\n,��jxF�G@��*g\0_���� �XY��	f\r�m�9y�à������߇�>��o��(jG;8\"yA�3׃f�9��	L�����mgQ�[{�ds(Y�~��~@�@:	���Y�������6Fa\$l�)Ob�=��<Vx�Yuvx����č5�Y���������Yjuh��\r�/���c^�x� \r���A,m�*��yw�\0�٫��hu��7U̫HA{���#S��{>L�h�]��Ђ�&~�f�ÞѸx��m�]�.���B���&e�m��e�lH��+6ZĿ�(�\0ǅ�ٝ,:YPZak���Q�.������~��	[��-a_�:�ɜbPcA����/\rh���e���h��'���ui�Ӭ\0�=�m�\0��i���\0JPh-6�`rfi��=���mC�R\"�'^õ�Tq�lS����U�ݐ���^��Q3��T�.A�=&gv�lM�3@�-T�+P��� ƏQA.!\0�j�D[\"�W�,Z'�QRݫ�U&v�YX�[i0\"՗{Y �l��{�\"��{P�\"a�W�Z�d�\0B�PV.��mm=0�kv\r5�5�Z���ൾh2�4����lOZܵ�Oɖ����.,���:��F�Z('��`-N��B�څ�խ6��,�§a����a�l����<6�ܽ\0000�����@�lM����4����Zc�R�Օ��aloڝ|&��G�I�b3��\n��\r0�(��5[/�fH�\rŮZ`���L�^�d\$��LΐU(5-�[;��(��8*��v̓��~|�a6����4�d�l����\n���/�L��y�*>�2���?�������d!|�'O�(k��P6!i��t�x\"��I��\0A�� ��,�����7�b��z����J2E��C�\nB5�@!��F��h���+-�:�\0NMC�s��H�ہ=nA��;s�o�*���:q��B��\0�ۨN�n��n�V܄��4}���k6��Zʗ�_�tv�����3>w�9\n��L(�Yy-B{�����G�\$6ye̋t�d]�2�");
    } else {
        header('Content-Type: image/gif');
        switch ($_GET['file']) {
            case 'plus.gif':echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0!�����M��*)�o��) q��e���#��L�\0;";
                break;
            case 'cross.gif':echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0#�����#\na�Fo~y�.�_wa��1�J�G�L�6]\0\0;";
                break;
            case 'up.gif':echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����MQN\n�}��a8�y�aŶ�\0��\0;";
                break;
            case 'down.gif':echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����M��*)�[W�\\��L&ٜƶ�\0��\0;";
                break;
            case 'arrow.gif':echo "GIF89a\0\n\0�\0\0������!�\0\0\0,\0\0\0\0\0\n\0\0�i������Ӳ޻\0\0;";
                break;
        }
    }exit;
}if ($_GET['script'] == 'version') {
    $q = get_temp_dir().'/adminer.version';
    unlink($q);
    $s = file_open_lock($q);
    if ($s) {
        file_write_unlock($s, serialize(['signature' => $_POST['signature'], 'version' => $_POST['version']]));
    }exit;
}global $b,$g,$m,$bc,$n,$ba,$ca,$me,$cg,$yd,$mi,$si,$ia;
if (! $_SERVER['REQUEST_URI']) {
    $_SERVER['REQUEST_URI'] = $_SERVER['ORIG_PATH_INFO'];
}if (! strpos($_SERVER['REQUEST_URI'], '?') && $_SERVER['QUERY_STRING'] != '') {
    $_SERVER['REQUEST_URI'] .= "?$_SERVER[QUERY_STRING]";
}if ($_SERVER['HTTP_X_FORWARDED_PREFIX']) {
    $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_FORWARDED_PREFIX'].$_SERVER['REQUEST_URI'];
}$ba = ($_SERVER['HTTPS'] && strcasecmp($_SERVER['HTTPS'], 'off')) || ini_bool('session.cookie_secure');
@ini_set('session.use_trans_sid', false);
if (! defined('SID')) {
    session_cache_limiter('');
    session_name('adminer_sid');
    session_set_cookie_params(0, preg_replace('~\?.*~', '', $_SERVER['REQUEST_URI']), '', $ba, true);
    session_start();
}remove_slashes([&$_GET, &$_POST, &$_COOKIE], $Uc);
if (function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime()) {
    set_magic_quotes_runtime(false);
}@set_time_limit(0);
@ini_set('precision', 15);
$me = ['en' => 'English', 'ar' => 'العربية', 'bg' => 'Български', 'bn' => 'বাংলা', 'bs' => 'Bosanski', 'ca' => 'Català', 'cs' => 'Čeština', 'da' => 'Dansk', 'de' => 'Deutsch', 'el' => 'Ελληνικά', 'es' => 'Español', 'et' => 'Eesti', 'fa' => 'فارسی', 'fi' => 'Suomi', 'fr' => 'Français', 'gl' => 'Galego', 'he' => 'עברית', 'hu' => 'Magyar', 'id' => 'Bahasa Indonesia', 'it' => 'Italiano', 'ja' => '日本語', 'ka' => 'ქართული', 'ko' => '한국어', 'lt' => 'Lietuvių', 'lv' => 'Latviešu', 'ms' => 'Bahasa Melayu', 'nl' => 'Nederlands', 'no' => 'Norsk', 'pl' => 'Polski', 'pt' => 'Português', 'pt-br' => 'Português (Brazil)', 'ro' => 'Limba Română', 'ru' => 'Русский', 'sk' => 'Slovenčina', 'sl' => 'Slovenski', 'sr' => 'Српски', 'sv' => 'Svenska', 'ta' => 'த‌மிழ்', 'th' => 'ภาษาไทย', 'tr' => 'Türkçe', 'uk' => 'Українська', 'vi' => 'Tiếng Việt', 'zh' => '简体中文', 'zh-tw' => '繁體中文'];
function get_lang()
{
    global $ca;

    return $ca;
}function lang($w, $gf = null)
{
    if (is_string($w)) {
        $fg = array_search($w, get_translations('en'));
        if ($fg !== false) {
            $w = $fg;
        }
    }global $ca,$si;
    $ri = ($si[$w] ?: $w);
    if (is_array($ri)) {
        $fg = ($gf == 1 ? 0 : ($ca == 'cs' || $ca == 'sk' ? ($gf && $gf < 5 ? 1 : 2) : ($ca == 'fr' ? (! $gf ? 0 : 1) : ($ca == 'pl' ? ($gf % 10 > 1 && $gf % 10 < 5 && $gf / 10 % 10 != 1 ? 1 : 2) : ($ca == 'sl' ? ($gf % 100 == 1 ? 0 : ($gf % 100 == 2 ? 1 : ($gf % 100 == 3 || $gf % 100 == 4 ? 2 : 3))) : ($ca == 'lt' ? ($gf % 10 == 1 && $gf % 100 != 11 ? 0 : ($gf % 10 > 1 && $gf / 10 % 10 != 1 ? 1 : 2)) : ($ca == 'lv' ? ($gf % 10 == 1 && $gf % 100 != 11 ? 0 : ($gf ? 1 : 2)) : (in_array($ca, ['bs', 'ru', 'sr', 'uk']) ? ($gf % 10 == 1 && $gf % 100 != 11 ? 0 : ($gf % 10 > 1 && $gf % 10 < 5 && $gf / 10 % 10 != 1 ? 1 : 2)) : 1))))))));
        $ri = $ri[$fg];
    }$wa = func_get_args();
    array_shift($wa);
    $fd = str_replace('%d', '%s', $ri);
    if ($fd != $ri) {
        $wa[0] = format_number($gf);
    }

return vsprintf($fd, $wa);
}function switch_lang()
{
    global $ca,$me;
    echo "<form action='' method='post'>\n<div id='lang'>",lang(19).': '.html_select('lang', $me, $ca, 'this.form.submit();')," <input type='submit' value='".lang(20)."' class='hidden'>\n","<input type='hidden' name='token' value='".get_token()."'>\n","</div>\n</form>\n";
}if (isset($_POST['lang']) && verify_token()) {
    cookie('adminer_lang', $_POST['lang']);
    $_SESSION['lang'] = $_POST['lang'];
    $_SESSION['translations'] = [];
    redirect(remove_from_uri());
}$ca = 'en';
if (isset($me[$_COOKIE['adminer_lang']])) {
    cookie('adminer_lang', $_COOKIE['adminer_lang']);
    $ca = $_COOKIE['adminer_lang'];
} elseif (isset($me[$_SESSION['lang']])) {
    $ca = $_SESSION['lang'];
} else {
    $la = [];
    preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~', str_replace('_', '-', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])), $De, PREG_SET_ORDER);
    foreach ($De as $B) {
        $la[$B[1]] = (isset($B[3]) ? $B[3] : 1);
    }arsort($la);
    foreach ($la as $z => $ug) {
        if (isset($me[$z])) {
            $ca = $z;
            break;
        }$z = preg_replace('~-.*~', '', $z);
        if (! isset($la[$z]) && isset($me[$z])) {
            $ca = $z;
            break;
        }
    }
}$si = $_SESSION['translations'];
if ($_SESSION['translations_version'] != 3062950237) {
    $si = [];
    $_SESSION['translations_version'] = 3062950237;
}function get_translations($le)
{
    switch ($le) {
        case 'en':$f = "%���(�n0���Q�� :�\r��	�@a�0�p(�a<M�Sl\\�;�bѨ\\�z�Nb)̅#F�Cy�fn7�Y	�����h5\r��Q�<�ΰC�\\~\n2�NC�(�r4��0�`(�:Bag8��i:�&㙔�y��F��Y�\r�2� 8Zӣ<���'Ha��2�܌�Ҟ0�\n��b�豌�n:Zΰ�U�Q��ŭw����D��mfpQ����q��a����cq��w7P�X3�t����o�	�Z�B9�Nz��s;�̑҄/�:����|<���4��j�'J�:0�rH1/�+��7(jDӊc��栢�0�K(�2����5�B8�7��\$B�/�h�8'�@�,-B�ƎQ��E�P����#��O��7�Ct��\r�`�������j������[z0�c|9�h��\$>�\0�\r\n҄�=Û���\0x�\r\n���C@�:�t��\"��~��8_)���9�xD���jΘ��2�(�-xx�!�H㌣��.-D�;����W+�8�63��@Ɍ���^F+���u����\0�ᠡ*,1,�i�8��cxؒI¤f�ۣlZ�*��/c�s�.0�0���~0���YWB0�7U�\"����:�3�xuc@�#�пC`2'�3X�Il*8��3�ʮ�߷����� ��5��5�3�#\r�9��{5��Uw��xj�0�B�͡�.'(��ǧh�\$�F�Jŋ��ļ�C.��!n(��hڂ��߂\"d�&��Ρ�jjn{��)�*:&&P`^G�B�?�0�6K{��9B�އ�#p�/���;׬�&7s�5˺\n�t��u4`O%�b��2���W+t(lB��?R�uv�3�J�i�^��ź��E_?3H�,�c���@x�}=�S�AP�5E�g�G�ԍ&7��R��}�a.mOT�s�J��lG���JZ��G\n�K\$W���P.OI��(�T:�Qj5꾐䤔�{@f�M)Ƶ\0�����\"�<Ȳ�J!���Ԡ����o�d���O�BB\r��7\"�Ȼ';)�������E�X0�f�杫����8ؐ�k4�Lد�C�;-����z�a*@\$���3��r�Eó�\r������o�	�2�A���*E��34���\"^�t�q��1�\0��S�怌3ܙ�A�?�8.� ��n3�6����e���ɉ4H�C\naH#xh��SJp�\$E'x�A\$����O�0�ȍp�L�\"f%PF9�c�Z�t!��2�V˹����E�@UÉ�(�d��W���+;!���3<hq�yNMl���Th�	\"T���HVa�Ά�A�Cw{h�&k�\$`&4��fhs�\"�R��p@ّA&L�`����kf��O�XA�~'\n|���h��Q�\$��'��P�*PU�\0D�0\"�֒���)!�h\\�R�	��������+ӞG\0��TDk�'�`Qe<ce���M\$�Ӽ��31f�%3�`���M�W���Х���(76L�aFAYI��APɛ? )���v�a�0a�3���c�|d�%�X�x��Ģ����6�j��r&���ȋ%iɳj��\$`�f,L�e�>�M%{'�a0\"�a`Ұn�Ǔ`<#V��K]�F�L�\$�t��\$g��,���pe,�\r��0ַ-����*����d�8XʣС\r��Tq�V�����U����-�P�cM�<\r'�xUB�T!\$	�_�y3q�/�ֿ%�)ۣ�g�����\"�;�|�a(Q6n�*�XE�xKO+c*ww �	�E����Ǘ�a�y\\��`͚\"1�f�Voȼ����ɹ������BX.���-���>a.��1ԴP��'\$���2*�PN��y\n?�vx��0F�� 9#��(b7��۔���!�����w��ס�6�0�C�,�,�#��QW�m	&p���geN�9_i�x�u�3-2#m������;&�]ה�7Nز��D`��7�_\n�K�v[�C-��;�w���Xj[8��y�R�&��ų\rį��b��e���\$\$A�����䂗��d���X����b�4puч��iЙ�<|�K1�l{R!���z��ͣ��_��<#�,�K�D�~D`��S������n�Է��͢�v��ؤF{G���ٸ�%Y��l(���vi\nX���\nܠ4Ι3��S��K��s�b��SAO��S�Կ�<�\n�}��_�QPK�(UIp2G>�i�މ�\$h����S���D��&W�ր�_�~b�9Q��>�#�����T��\"�y�F�3O|��r��}��Z�ޭ<_>ޒ_�����|�Ʒ�b����].���x���?���8\r__�Ɔ�B����P�\"��X��x,���k�9�2\"|�.�6.��.~�����.���p4��\0).��|s�\0�PP���L��W0\0lpT�PfB�#�.*�\nD���E�GL~���.�����	0F�,����	�ni��6o�r%����л	o����F�lp��EZ\rp��F��o���Ц�fh�g\"Äc�yP�W��Q\0-,:�P�#��0�wQ���B�\0��7���+�I\nB;1��#��<F&H�7�N�L]\n,�ކ���N�`�#b>m+N2�R�qj\$�.b&7�bt�.\"�	qm���o\n��Y`��(��\$4\r�V�����zMN 3�(�\0��./%�f�N\n���Z�5��9�Ώ&\$L���\r��-H��n�mk��/�����z�g� �j/12(b�(LG\r�e�̞�g'\"�ZS�&Ib*c��:)�@�N\"Z���\"���El`	&Qx�.������h�c\\�~���O���&qz�Fr6\$��N&j���`lK��ύ˨���r\n�9�J���,Nhj*z*�N�c\n�d��H���*0��ª-��&z=��e�e�	��F�?�|�D�0�[��,�\n@�-�\$�snȢ��\"�-L`BĂ[\0���#o&����sG4���j�sP�";
            break;
        case 'ar':$f = "%���)��l*������C�(X���l�\"qd+aN.6��d^\"����(<e��l��V�&,�l�S�\nA��#R����Nd��|�X\nFC1��l7`ӄ\$F`���!2��\r��l'��E<>�!�!%�9J*\rrS�UT�e�#}�J��*���d*V́il(n��������T�Id�u'c(��oF����e3�Nb���p2N�S��ӳ:LZ���&�\\b�\\u�ZuJ��+��ωBHd�Nl�#��d2ޯR\n)��&�<:��\\%7%�aSpl|0�~�(�7\rm8�7(�9\r�@\"7N�9�� ��4�x�6��x�;�#\"~������2ѰW,�\n�N�l�E���Rv9�j\nV��:Ο�h\\p��O*�X��s��')���ir�*�&�V�3J;�l1�B��+l���>�j�\\�z�1,��t����*��4܅N�A�/�ډH%��-�=lLHBP�G)\n�\$�R2�E�t�,��]4��R25 ��k��(���3\r��1�C��3�5�1A�(�4���.0�0�@9�`@Y�@�2���D4���9�Ax^;܁p�V��T3��(�ã�?��x\r�KJ���H��\r#x��|���1mNR*))��U8�I�\"TL��\"8I�[R�3Qӌ>��,�j�\$��W�B��9\r��\n���0�!VP�H9��CMyR�SDBY({*�Q��T����:����0�����E��\$���D���)*0�)0��kZ��JJ�I�16�HR��Y.�\nG�������K���k\$#kch�5� �0���5+\$�&-k[�9�6]M�TlZT�=&�g��N�Ĉ�X[���1�r���/t���ّQ�!�oݏ��#��ۦR�J��BǕ�{!��L����P�!N}�s��Yt�\"7��J�L\n7~Q��9C(�n�r�`H�E�����t\r�(�fiCxf��W�S��4E�1 ��y�_�<�\0�\0C��W���\0��9�kH:(JC8a=`���@��pu7`�9��\nI��c�iDFtf�m��*\$\0l������Y2�p4�E`�\r\n�[+mn��¸�*��9.��Г��w� }X�`����!�\n4\$��4\r�RD3�c�A��D^����/)R��@Py���y�\n�iZ�i�XԶ���\\�ru̺r˭v���\0 }^�\$6�^Wpt�`��L���M�?!�}��\r�K.��ʑFR��.d���#��\r*��l,p���,̆rC4�C�yB�%�X���N�f@ r�d����\nF⼈�bDBhOA'�`jC@p\0���ZI�#������F�� l\n\\�8�f��5���Vr��I�7�Ue�����?a����:��.&hБ��΁�ٳY����x�\r��\r��!,����w7��4C���� �4ҹx�I�L#��0��5Q��E������1%E-NY6���{#�-C\"U�`�5\$��QB�JSLGH�GY�L	�4&���)<E��KFo)&M� ��\0I a�ϫ�Q\"��٭p�׃�� 6��p�+\np�PZ��M��#�(�2�xS\n�X�0�MK�Z�'�0^�sH&dD���w)U,�>�����]�14��6��ٴe�\0���r����ΰCox&\0�O�Y�*r��i���da�pC��AG�㣒�+d�%,5/��(�ظ\n	�8P�T�\"@�.JOq!�2�0�r�}v���=��_.lx(�b�E)f><��5_,F��E��\$�4L�k*U&(�F�^Fi%�-��&YaIuU�����6L`1�u�I�4��/a�Q��hF�_�2:e��Y����U'�N=�x�I�!Yw2#��II`>��?@t��Y�\".�&4����=�Sx;���U�v�����v~I6�}�u��6�Q�A�G�)�����ňM|)�S_M��!�M�<�KlS��|� j7;95��;����kf1I�l\nGíN�#��h����r���ĦD�鸒Yqy��f��ETR�&aH���:k �\rk���Zwp=�?��;O\$�챍��Rr��l�A�hMA���d���+�2\r��_���u�ϩ��,�C	\0����)��\0ݢ\$ʥ&��%���������\0�|\\φ���U�C�,�?~B�/iT�y}dC�1��U��yz�t�_�kF3���Xh���G�F�K`�Bv�^Y`�\$�\0���V{�>F��TkS|:b�FB)��W�@PL�X�r\nD��*(}���J�ִ,F<�K���amO_h��BU\$S��ʄ����CSi+\$\$����X��F�Xpb�b\"xF9Ap)Io2�G|��dwC2\"BǄ��@�dHP�@�\r#kHeB��d�%8e'�o~F�{!Y0H0M\"�yFLT�L�08%�v0�{�x|I|��E/�.P�K,�\$f*Nk\\SL�s�P-��ļ|��Bx�h&D���΂�p�g�L�0�\n��JD�\n���p��H�-��H/��p�QDh��Ͱ�K%\0-���m&a�`�iqb�ER�T!Xƭ��!0L�0#lF�P���dF8�����n�prP����w�qNcp��qpz�\$H�oh�����lCZG��L��X�o<P�p�����\n�TF���G�P{vGѰ\$P�#���j8�S��)lv ��\":���J0EH�!����P���8�fp��ZB���# ���������n����Gc�����qZ�L��-�`e�\0a�+\"R ��`�Uq�nX<���M��#�?eF̮����)�='g.��̫)p�\rF�����*f<��8�AW�t�db-�*1k*kE���+�w*��,d�9F0-*�}\"��c�;�:L�8���20�8걐��)\$i11R�.����'m���,G/��2R�|1l0\"\\K�.3;	b+f�c1F1S���?E-	�7so)PF�k6�A-��83K-�s.3�3P_6PDD���p�R�:�2ғ5��43�\"Ӣ!n\\�BA����V0��n�����3�6i�<渔J�<3�\rs�<�Ρ`�߮�&�V\n�>�8�O�1s~yTHs#94o3c�A�7,��B����!-2�@�f�TP!AP'�F쳧�E\$��:(�1�4�hl�\$wc�<N-���<�\n�F��2��1�������6�DV��s\0mF����z���o�V�:�>&�n\\���k�\r3ܝ��kf�7�D\r��\r ̄��\$\0��\0ڋd.�hv\n���Z�t�=�L�G�O/n\"/6g��!Լ��d��; �NT�&~�&�v0�o�(�0�r\0E8�2L�U��'��\nmX\$\0�\r�._%�XC��5uP��.�(����B�M\"�ob�&��S`\$Ĥw�\r��ͲĽ#	2�Iĝ'\"�[�0��[�̃��6cD4��@���U�G��Z�Q\$\"�a\"���KR3SH�0�\"A\$1'n�w,\0v6P��}(.�G��5�X�Q@�W@���d\"G#t\"Fq'D�nD.��>5�<�:c#KWQ��Hk0\"L�e���0\r�d�V7d�6��ϒ`Q�dnF�J\"dGC��F`	\0�@�	�t\n`�";
            break;
        case 'bg':$f = "%���)��h-Z(6�����Q\r�A| ��P\r�At�X4P���)	�EV�L�h.��d�u\r�4�eܞ/�-����O!AH#8��:�ʥ4�l�cZ��2͠��.�(��\n�Y���(���\$��\$1`(`1ƃQ��p9�(g+8]*��Oq�J�_�\r����Gi��T��h���~McN\\4P��򂞴�[�1��UkIN�q�����呺6�}rZ״)�\"Q�r#Y]7O㬸2]�f�,�������D5(7�'��1|F��'7��Q��Ls�*n�����s����0�,��{ ī(��H4ʴ��\0\n�p�7\r�����7�I��0���0�c(@2\r�(�D�:��Q��;�\"����>P�!\$�p9r���낏���0�2Pb&٩�;BҫC���2i�zꤨRF�-�\"؅-�K�A���O�łJ<��\$i؃�,��ߚJ�)�(f�l� Ě��hQ̴-�r�:Hz-��;RƵ*4l\nԍ�K\$6h�=?T��av�W)\n7(OƁ\"��O�L��f�\$h��ii�̝;�P;l# �4��,g���w��\0�1�q��p�TgEфd;�#��7����H#��\"Ɂ��4C(��C@�:�t��,6����@���p_��c���J�|6���3ElX4��px�!�\\���&��Nz�e7�iCT.)�>�6��N8:����bz���t�jJ��h4�S�b�ʰ�(��(��B��9\rׂ\n���O4��N�%�*M)�뎕TH��hp��ңH1 �)<S�H��d�6�t,m�?\"h&��I�%�.���g���х\"⊿	�qYK�o<\"�U�p�s�봷4I�r�A,4�K�-ht�z֋ �s��;����j�)Ϋ�;���z�%�v|����k��չ��(��İh�9�25������=md��Ƣ�ZwBgT�u��Z�k�@���T\n�s����V��`�Ԅ���w��\$b\n��9��B�A'���4��T�<A;�'	�<(~������ BVP���6z�!<P\r!��dV�(D��.��o�\0eѐ��j��q�N'�ۆ���)(1�<�����^B�P�x0���a\"�`����S�0�8�G�F9�)&\"	����d*L)�i���L}�kh>�i��8aO.*�J����J����I�i,a��\n6GMd���(�ʽ��<�J�q-Ť���FX�veLT�ND�9bq%H�>&aP4<�ـr�w���V��I01���!�0���b�݌1���C�d �F@��\"�5���n�J�N���(#���M`�i��C(�,�(R�]��/��䔕&E����\")�n��6M�*��}O��X{blU������ dQ�6��DYR\nBf��D�E��e�jzc�H(U{�:�������)��u	`-#��ʗg�ة<2pGLZ�8&��CE�Z�B����0Ќ�]f��|��2�t����Cf���9�Uػ�0u��7�u�e�h�E�<	�����9�9�����	�	!u�����-wV�Z��@P����)T�ZAA[%����Ĥ��3l�p�:;��o`�4�`��=�`�Ŵp���D6|��P\$�CU=U+���D��Zr\"V\\���sH+�Z�i�Cpp_�!\$FMe@iv�o�vf�a�v\\\nQ�:�L\$�/��pw[�&#DG*��%�t}&�S\"m�T%��\r�𒌔�-�9G�sbC���)�}�S�stE6;�?�/r:H�i-��2De1[+�=,^H�w��krf��,�8uۮt�T[�	���f-�<��#J��Zuz/ܹ�}�&8:���Ki\$diM�xJ��B����j·�a�,�����Ϟ	D`.9��3z��ʸ�x�'�hST���,����H�Kp7?��USIW\$Z0`�v����RG0�\0�2�,Ko�Y��,ڕ؅���sytS�g�2Nfơ�,����]���V����9������0��uk���\\	O'fr	�2~a��8�H�A��*f��02'M'kT�(|Шsd��\n:ʤ��ש��F8�.A#��0�� ���Y�E�cZ:�J�u�Md܂�j�@q.��a���Ж�t#����*�܋�۵9��{�C���6kk��u����r!�`4 �V���n��ut�}�AF��{�D�Q�j��l�rc��4I�o�ۀ��E�b?<�(BI��fؙ��U�0�z)�Z=#6�<_`�\"ӣ@�-^{�2l��N�)������U�l��8<0jp���|[��蹄S�- �X������v�)<!���\rb\nfE�d�`���A��R��b�/���3B\"V�x�hhmH�G\\{\"�(L�q��+��4h~��8�\nڨ��*8O�h�H�HN�Hz�@�C< �\n��`�\0��J\r\$l]F��t���H�H�2Ң&g�<�E�u��o`^\0oe*��n���C��*���v�̔6I��>��9�!)���O�<����#\"w'֑Aa\r����B:��)��.��#�Y'��O��.�Q\"3\n|�Ó�Z���1��Idoh'�\0�P�\ri���� �6'.F��e�n	G�v%����`��\"����T���.�umN+�<��Һejq�GM�x��,\$���ek�Q�MN�~\$�	80�����_�D*�Y1�\"0��B�p�ў3eB��t|�{L\$iGT�Ɋ��D7��BInmD��ʸ�0�b�+�(�G���2(����z�\$\"�R,6O�'��jt���(�d+�`�/d������믅&�b�p�RE���-҄���(�xއr�&�����kȘ<h�\"ʅeds�B�	��l��P�1�[`�D��L�\\u�IQ�(�=.O��.1*�H�%�(��+�,#0|��Aq*Բ ��\\z���)��7'�NH���X��E.��vS���Jl�)�ֲ4�Vz���n�*=4�#\$r�>��\$'.���4'^�3`��dNf���5�E5�c62�'� ��7(F��u4�H�.���,�(U�192���M8s�����3~��ӝ��)�TXӸ�b;���F�*�i�A��8�{p�(�X�>(���<��,��i����S��5q���o�:3�:�5s��C\$ߔ5=����#�C�;r�3�*r�qB⮬�N�B<Ps�c���D��Ѓ���c^�'�\$�tpĘi~7���2���G��ĥ��|�rB�h���m��ӆ��{E�EN�2�'3�4��o�����]J�g.�|�Ԋ:�7D��x��j��Y4�'�C:Pt�{t�;�]*R�%�E�2�&j�_��|��'sOC�(�S��Sɹ:Sf��\"�ULٱ�TIR,%8j`���HRw0]�+RQ��wW��\"�ǰq�#pxr\$Z��J.p��^����&��P�S�D4YT07:�IC�A0WDtP[*��5��<� N5-<��&�4�vm�?2\n�s^�[�2N��_�v��`��~S�\\�]�<���,C���P�9�\0'�)��QU:��0�4��}c�5[�g^U%^�KZ��cleDe�W<��]���Pmc.cf�Rw�~h�`�V�H��/6�s)K�T�p+�#���V�;�^L5�_�8M�Ga��i)�lo�\0�֟d`�BFrسp�m/�m�n�6޷�F��n�֜���޷ �\0��(�\\P\r3V�Q��dV�Rmq�rC9q7r���W/VQ83�k�p�Ut~�-��RՂk�RV�>�AR��}tR�]0Ct�<vGQvc���wkj�gWIo?xe}x��V��y!K(����AoJ��oxs��)0����\r#�P��yBq��`\"�PδY07}BB��p�!�U�?�FKy~����\"�,׍?�&P1N��:��#�C�BQrkԎ��b�K�?Adޒ�f�><)E��x����L��u��I�jD�\0�\n���p�N\0��\\&p�Z��ݨz��6z8?v�1�aJ�����z�&}��uեlȖ�(���KNp�أ�J\n6@c.B����E����D���l�hs{��R�.�/�I����Y��>���2��x�X�z!�bx��Q�і+R�+Mt̲_J\$�)�0�mMm8�ؙ~R|��_�x��S�xx�Q��o)����;M�Sri�MY[�㟔㪓-��m1��Y(x��o�SG��ǂV՜6�%7����m�:U�X���SOj��N�'�J�i%B�l\$�1/��CQ�b�8��Obn�9VpbpJ�-%��\"�y�2��}}��q��d���p�C���{Bϟ!��*����=��7yȂ�R�nq9e�@��J�荖c��e�,I63�+>���E�Zע�	\";WdQ�l";
            break;
        case 'bn':$f = "%���)��U���t<d ����s�N���b\nd�a\n�� ��6���#k�:jKMŐ�D)��RA��%4}O&S+&�e<J�аy��#�F�j4I���jhj��V��\0��B��`��UL���cqؽ2�`������S4�C- �dOTS�T��LZ(����JyB�H�W΢Jh��j�_���\rmy�ioC��Z���N���r,��N��%Dn৮еU��8�O2��n�ŭr`�(:��NS7]|􆇵��8�2ɼ� 4N�Q�� 8�'cI��g2��Oy��2#��:\rK�:#�:E3����n��m �;K��B+�M	�Ь#���G�.��S9h�����6ԫm�T���d��\n�Q����D\r�I�l�j�'��@Ep{�����L�D�Ц�\r#pΝ2�*b�+\n��D��N���tɨÄ�+H�*��[�;��\0�9Cx��0�o`�7�/h�: ���AR�9���C��7K��Oêx߫��N������%4譖�}4�k[Ư#m�q8	��CP{]G���:�\rQ-R(1T4����7���AM��c4	Қ��D�a���*�;6B�j(��c�֍et���Z��-�Q���ElL�t}C�E�X'J��]6\n��Y�4]f'�1OQa\nB�*9z,i�lK�L�����6�JvP�;B�6˳�AEѯu 9�T�@0�c�9ˣ>������L�R�K�#���4C(��C@�:�t���9�sA�C8^2��},9��^,���A=���A\r��7���^0����Se�CIxɵ������|E�Y�@� �B��Y�QM�^\$L�p^2b�e�쳕 �Ecx��R��Cݥm4C.��4�ˌ'����4B��9\r�J\n��M�r>Ǵ����-�.�ϊ�W�k�*/�zN9]c_�u/as��IN�*k��/�PDlA�:�� �l��q/��p���-��S*�:r�|����E\0:�*H�N��L��'e喺�d�؄.\"�mU�K!�C\\@>���S2CCB|�����/&e�,����F�������Aa\r�()��Q�G�������+Ck6!A�B�C2e����b�\\���	��%��������;g?]�\$X�Dt.�\\�Q�1��\$Y��0����ˉ������cʔx��PF�y*\"A@�q�'xF��'�BC<FT�.1����	i��+�Z���n����MZC�\r�!�7\$�ѩqvɺ yO;�=��3`��\\�'\"\$���y�q!�<�\0�6ëEh��\0��:]mp:)�C8aK�����p����\"xVQ�\n+q̍�!Lf�D�DY�\nd�q��T=)��8p�?�B^ڋ�����i������+gm-�����O[�rn�����[y@��8��㜅L�Y3/�X����#f��rV�5/k��{+JJAٚ�xh`✀��7om��'��KaSmu��v�ٛCjm��;����.n�ݼ͉�7*Ӂ	!�8`���`�PZv��O�!�©T�>�EbbPY�H�`�M����:�p�bqB'\"�ƉV` j!�*#��6��d0�k�h|��R�Д��P!�\r�9�N�����h��<MT�U7:����.�@\${,H<-^%^��d=~ؕ�;�����V��G����r����S�b|O��>��0��Q�V�����D��\$zX�X񃹭����ԳF���8P�(�Wo�e�t\n�E�\r!���z�dY�%��-#�8 aL)c|w��-)����ٛ�L�7G� ���5�r�s�^tw��hVby1��)	F3/TdA�2�^1�W�P)�C���L*��V��Ϣ� ��&�W��1�׵prI'���,fAŰ@�鰇ԀfP!���vu�S�c��&�c%,~��D��X�r�O\naP�������75 �ڈ��*��E�w(�b#3�ex�6Wm�s��g����Dn|�kȲ�O�n-,1e�AB` بF����Od4�F�Ը������2tVI�zC��G�,����Flβ%,��W��,�tm\n����j����yq��ďB��b6�)d-�U��ɝ��\rI՚�x���)ĭ������2I�Y���'�7�L��\n��-�p�������ںbR�>jU�֑Ć�RR9&c��w4@Y���7�F�\n���D�Ɗ޵H:�+���n�G圿Z�ns[<_2�|߰���]3j�:r�`et\\K�_8�Q:5(��y�����?%5�]�0:��qV�L\r�et��PWS�̵Rq��U��\0�5S���FX�B\$�c|҈�gLx�Z�����d���H�+H�(\n`�>�Hń���^L��O����;�4�bl��O���{#���'GX��u\0)BZ�%���C.|�`��{�2VIw�xN���\"�,L��n:�̓�Б����4N�ҏPJ����~k�`� ��l0B��\rpZ�#T��얍Rߣ�\n��*P�*�uO���~��-����4��41�J�}Ξ+iJv�>b��fh�\"����\n#/\\[��Ne�g`��x�	x�fj\n�� �	\0@��DQ����\\@E6'M��x)�h�->�B�/D��{ ^~Ee�4�{\$5��Q%�v��-�CÚt�T�\nZ�B��G^�)@�#T�����VXaH(R��Q\"9�_�����,16E���Gv�jW�zW1��F������J�>�d<�*0\$������-CQl_��Ej@��^��� �6J�1��/\"�#(�\$���q#���,��Fb�Js�N�Q���\$���f	�J��\nWG�j�3��#.^Wc��*J�4�B	,\rg� �x��.�&��r���\"t�\$��,�\$�)��!��*r�W��Y���&YO��OJ�-�bC.��zx��D�Т�/�i��0��,v*/�bGPG�	��Vp�,�ބ8S-*���.9�(��#n�^'�TR�}��)0�H-�]'��\"�����6��/N�,<�_,�2�^�d�皐��o��a\n�\n\\{r�0\\8sp��*�Sݓ};��|)j�0z5P��3���X3�-���o{)#=�,S�=s��n���G3��%�uN�)�D�Spu(P ���Y5n�/�J�ψ�#��0��c�4^(-Dt2.*�@�[@�}N��3&a/?5L� �[>s�@��h�R.}D('N�΢��E�X�m��h���{��;�G���*󄪬�Hx�T鐂D4y?�Y=t�J����J��G���)/=�c�M��J�ΘCT��Gԩ7�O P�j�݉s3�;�P]}@4��M�'4s5�=L�\"�S��btV�KO#����O�e��4�v\0QF�4����2mq�G��Q..�T��Ot�UTKU��U�|�d�%�V��XQR��ꒆ��<���ON2\n#\nI w��O�OS�Y�[�d��!Q�L�QZ\0�\"���8AUtH��î�b����t�݉*+�T#oDb����j����%�B��Z�_�G04��^�zL�&B����M+6K_p��s�3�(i�,+��k�=u�;p���(\$w,�=��	p}I��e�\r��O5D�V�\\b�\\N�6��\\�i�����7D����Rtk��Y��j��k5ؗp�A+dt!Z��[�l�+����� t0�7Lv�P��Mq[�]�l�=v�=r	SDc4�So4-�m��t���o(��G&��6�!;2�t�tUn��;e4r��(HD%iP�T)}CG:�5<�	k�w(5G;\\�h��l��S�@�s5�yS�j�d\$�7chh�y2��h�qtG����x��V�v�䂗�mw�{��W�Ve�w�]p5q_{�>�b�s�\n\"qd��JӂG<o�v��mUPי#	}��YzYq*Wu��=qf��\$�!o�a��MRK\r�x�=G�o�'sx}�SV^Q:��((���w�τ�Sz��H��?U�u�82}8wj����igU��E���m����=���owB��8��c�?K���,pX�\rx����0�(gL��P�\rG�_F��{Z��qX[8�\r�|��{wm[8���\r���������)!�\\7�v�a���J�}��~��n�q�\n�Q,��0��<Z�w��\r�Yb\\�g���Y(X_zUq9�_R9~\\��X�l�1zx���Co�iwu�2\\Z�4���t�\"+s@�g�Xw�Y��,�u�c?+��W�3W9�yѾ�9�'y�F5b{T+w�٥x(�*9V�N�*EDq�UwI��p�a��5sfV0�x��\\�ywO�����m�\r8��&��@i�\r��\r ̟��A�����e ��&\n���Z	��^K�����VeY\"��㸕U�[Bq%Y��]�As��ږ��5ɠy?�+V��yam���	�]�\0�(��Eטtt��Y`4^:5���v���2J�?S1�o��^!2]G��X/��T&��	���gȄ\0A\0�F�i9��������\\��ۮ�H�>Y�A�C�?2�Y�8ڣ�/�8#�e��𐜬٢N�KO�o:d���B�;{~��=�^8z���琹�'ਙ��=#׋��\r��q�%@r��AcϪu��F[��Yg*�M*D�I}Q��J�I�5�Ԏ��	FR3�}-5�ѳ3?b��%�/��V��d���`\nƈ��\r�b3�5C����z�:3��2�+�@}���΢/Bt&;e���H�`\\i&�!\$HY�h��[}y��Ǩ]��'�.�|ے@���>D��9�W@�F�Ü�j9���1�|	\0�@�	�t\n`�";
            break;
        case 'bs':$f = "%���(�l0��FQ��t7���a��Ng)��.�&�����0�M磱��7Jd��Ki��a��20%9��I�H�)7C�@�i�C��f4����*� �A\"PCI��r��G���n7���+,��l�����b��d�Ѷ.e����)�z���Cy�\n,�΢A�J �-�����e3�Nw�|d���\r�]�ŧ��3c�X�ݣw�1�@a���y2G�o7�X���搳\$�e�iM�pV�tb�M �U��k�{C���n5�����9.j��c(�4�:�\nX�:4N@�;�c\"@&��H�\ro��4�n�\r�#���8@�@H���;���*�\0ߨ���\r�ò腱�P������.\"k\$b��#��{:G�s�h�l5�Ϫ�ҠϠ�6���J9>0��Fî,�&%i���J��3��*���5'-��I���)#�U.��AЀ�1�mP��S<P(@;�C5IB#�'\n��\0x�����C@�:�t��t4:p�˘��x�	B����J\0|6�hbp3.cj4��px�!�=/��;��=����ʃ��C� ɋ8�?�C;N2^w��|6���'2�LQ9��\n�L �,MN�C�������!>ãdv��CH�4��r�4#� �kkҿG(�0��d �c�	p��P�:��\rpb^������p��5�8ɤ�&!\"��V:5�;�#����'��֜��M�5�T5�H�C�h�ӱ�fӠ�JHJ��nz��]�_^��:=3w\"�9�|k���w��j���ĆF)A1�<z��s�����ખ�O*X��#\n��/�<�]GkCM��g�#h���[V����n�(%茣�\$7R���	��M�7�!�x�3&�J@'�t\$�7����<�>��JR�6j�\r�:p�JT~��������eA�0RH�����Ue�\0T3��խEF�� P� �*����I�QJ��*�`����W\n�;��|�VrXk��V`>��\\񭵺H��,�rRFk!\n�A���9p%�\\�%��2�D�T8/s&�R����Y�Un�Uڽ���ðܱ��{1�f�0|[��d��#���D�BD'����r~F :!r�C�*�,ĝ.4J�KE��D�CARp��*\0��J?�x�\"`���\n��K<��j�/����;��4�w�^K�*O	�'r*2��&�`��H\nIԒ�PPI�0䨦��H�)!���3Xh���4�)VmJ���;�rBL�L;����*��5lc�X0֐n�R�u��͘c��3�)cF&C4��'P��/���aL)g\0��\0;SYE�*ke�u\$\r)k9��F�kR�X6f*��S�%� 4����B&�lޝ�4�&xI a���S@e(n�f�ՙ �MR�\r�5~�����z,L:\0��MC2D���`�T�N���r�C�#�)��'��XR�^b\$1Mb�S\\\0nŔ��5�Rk1�]撰�JO�F\r��M1R��~��RR���pI�H�Z�]%(rL��*����6��)���B�#H<<'\0� A\n��FM�\$d�&(BAH\n���\"P�o��)T��{�\"F��1��R]��D��2�W�0�.��86�����;fN��t�aˡ��h�㬸��u�Il��Zm��t��Ź3.�s��lͼ#��{�g�����4�vFƩ�'8��l\r�W(.z�a�q/���#�j�>P`�C���I���fP�\"�~AWD;\$0����P/��xH�]k�e�Q�\n���7�x'�v�y��y�A�wΒuE7���M�л�tLiC(w���ɤ't��MjH�)9����JgN�43Loܪ�k`���s�{]�(!ʐ�R�!��#c\0��q6=z�W���pS�#'\$�pR6m��!�()2�~W��j�?�HL��MG�y�@�BH�q ��Zz\r.dI\$��(QQ�A��y1@^\0S�L`L���&S1Q��5H\\5�	TeSČ� #���\\B��D3��~DqId�sồ�n,��֭l&�s.=���4<���{�L��}�k��H��~A��JN�zot��(����i� �J�|�r�)�q��߯����O������������I�d �� �h�\"�&��yI�Mt�0v�a�'���˔�!E��(J�JgI�Ck7��2�T��Y�i���s.���5~PZ��\0�}����i!�e��vzˆ{7- ���1��S�s��L�S�����u�Q���0#���BN���,~3��4J1_`F�<\r|\r�~�����N�8G�&���+�\0�-P0�0.0&!f��u��\n��=mR�%.���ꨃ&D��I���,'D蔋*��p���i��-��CC`��d�ul�g�u��ɏ��tM(����w�	.�\$�ZО����bV�,<%���e➠����nX��^���'\"1\r\"�\r�&�0�s�-'Y����c(2���M��~C��&��\\ˑ�\r��`&���!	��e�p�P8Ƅ%�Pc�>!��O�6i��8����dP֙\rn'1P��SgJ��X�P�%#���m����k��\$\"�q�L�/�q�mm�Rod���gP��r\r����HDЩ\n��\n��_q<�Q\"\$Lb�����jÉ\n� (m�\r_��'W����_����U ��K�����l<�G&>\$� �R=ogL\$FIF3Κ��ܑ��ܿ�.ܰ�ß2I%o�	Rr�^F�\"���2�E+���)%!Rlm�q(_x2b�c*-��fF���Q�\$'��ii�Y*��\$t_,v2w�d0!*��\$� �A&mt��+�d'\0�=\$�dM�٣89	b�I�\r��͊��/�V0\n�.�0��3���� ʓ\n&l?͙0͜��-O�}s=��%�\"��R82�+��!M�5\"m(�]4`�H2�F�J�h\"��r�M\$�7�X�1\"C��=�W ��0�.2T�gV�C�	�P�b��5n<K�Ē�);n�]�peN\0ʏ�<���&�n�\\E�A�Ba�i�~b�����n�D�!��Z��(`��q��J��h�9M��#��\n�g�MB4B\0��\n���py�N��\$Q�h��`��1.����Tc��EЂ��T�6��g��w/�^C\r�`�p@)���`=sxC�L/c6���&62���fF���Oj��T\r���d�K�oL\$�o�-b��p@m��J.�rl�o�#�ǭ.���4�y4Ԑ�&�}ML�t�:��F�\0АM\n�����>�\r;R��:����iK�]lk�7G�p���wU�\$'\"q��#��D�C���\$�� ��v0x@�\0d\0�g��&\"h�r.�����n-�B2LOJT~^\"�ȧ!L�WZ���Q��K@���\"R�SC�\nC�A�J\n�>�OMD�) ";
            break;
        case 'ca':$f = "%���(�m8�g3I��eL�����a9����t<NB�Q0� 6�L�sk\r@x4�d�	��s��#q���2�T���\0��B�c���@n7Ʀ3���x�C��f4����(�T�P�fS9��?���Q��i3�M�`(�Q4D�9��pEΦ�\r\$�0��ֳ�X�~�`��6#+y�ed��y�a;D*���i��������+��p4(�8�\$\"M�<��k��X��Xē�YNT��^y�=E��\n)���j�o���M�|��*��u��4r9]��֡�횠: ��9@���9��Ȓ\nl�`���6=�:*��z�2�\n�&4�욊9�*Zz�\rI<H4���H���*������̈��;I�!/H����Ȉ�+�2���\"*\r#�&��!<&:�Oh��\"�D׌��06�r��0.�P�ʯ�<�\"�.(r\"\n\$��H�4��b���f�QM�&����P�2%4�34�C|	7�<0�c7�����>44�Y�����8�ʌ��D4���9�Ax^;�r%L�Ar�3���_!u���J0|6�Ȃ�3.ɓ���x�B��R���� �j:�`N�ʣ��:-̍�ǣ�5ꓵ��7�\n��Lc0�]I��+�\rP��� @1*���x�9���ڽ�RĴ��x�/8�+�\"0�З=��2�����2ш}��\"2+A�((�3��%E�i\$��6-c�ގ\r�`ߝ	�����0ܝ9�a�V�4���7�4�#��KK]�P�l,H�� �0�l�#*\nb����Z%��81Oj\"WxW�Ӏз���H���ϲo�=+?Y`x�2�sr0��6dh�!�.���C7v�K7G��,������a�/|\"��#>�I!�jH&C��\n6J�SH�#��3��&�&�SI�r�ި�Cp��Sz�Q�ůP�^L�J��0�p�^�1�d\ne\rPP�K�o�7)2`�߱�]d\\�C|��#(L�.����0��r`��X	b,e��R�/K5g���I{\\q���\\����9R�9�]�q�P*��&�Lk�?�T��D���\\�<Cuv�!��Xkc����EZO|�>���:t�R)�M���cA��ַ�J�RdX�\$28��T/�H\"Z*\n�Ke+HdX�CAT�,*���4P��1���b�D.6t�7D@�c8e[a�/��!�����0fht�����]�(���^�\0�����J�7�u��8[%\rc�61��;ℍ\nz�\"-�7�x�'f�ڌ�r�b\\��%�..�ET���˂)i�H��c\r��,6D\$�0�L߲��%ϰC\naH#P�J�p \n���� �k��)k�Ť� 	��k�~�Pr��i/&)���X�3R���@�@�ɥ��i�v�n��t�P������>����#@Q�|f���@�¡QGؙ�N��W\nQ͡�u�_�ւ,�E�f;̪�\r-�\"�<[b�Q�u�V'�P�o\"J�1���Ԩ\0�#IΤ��1TGjܡÑ�\$i�B����+�!5\$�%�p \n�@\"�\\E��&[�����k왔#��GPr%�@ʒS�q+q��5,�a�#�\\������s������xMi12�ٮ�f^�p���)ԅ%:L�b+'���Z5���6��'��ݑ�sI��&7`k[�!@(+\$����9R�X�(�f����(�z�tK!b�D����')��P%�\"9&ػ|�R�75���|��zh���4D�ъ�d<�W\\�#��&��D��bcF�2�|i���ECF��H�XC�aU�6�e�2tge�V�7*dB\r�6V;WF|Gn:B�!�5��Pni�\rm��4��v�9DT�٭\\��(nl!�a�yJ�T��wĩ������d\"��,�1c������u��Aa :B�l�mS��Pr�~��v0͟�@�\0\nd8ІV>Ʒ�Udm7v�%ֆ���O�!��(�.�:��̞�D�����8]���|p��P���Ro\\�pL�w|�\r���!�|W�q���8w.ߏ���:�6e�'�pJLja��8��R.�I�MD���TcŊ!L'Ȍ�\n�N5�).���rT��2/{�	�\\���0bxQ\"��]{\$���V�GS^F���ZPta�?:���0ŒV�x\$�p�s-V�J�S6�0�^�G�-�W�|C��{������\$X�\"�B-i<�U͍�ؼz�f��0މ�e�)���������ܒ�O�-a)R��_m\"�Q��F6w���5�h\n\nc����Q7�PYw�b�l'aH���Z��JK)V�'C.lEP#���Dp�t�4�lTv���麧���8�ϞÇi�#ɞ�o��0>��^�.�Gc�=�D����V�-o��g�t�����okbd�\r�FdؐJM�/n�@�PV�C���U�pv0�=���@�\$w\n./OOxP�<4�TRPz%�#\nB�@������0ԯb�~�Jd����/\r�0G4_�Q�NO�(��P��=�VC̼fNv�F�rtF^�0�#���ز%x\$JP�� ���Ƌ�;�e\0���=�����RC^���Ӆ���20T�.v&\n��b�p\"�\rpq���\r����U�4^��E������OJs�A���M�����\$׭00�1+��(h\"2�K��F_0!	0�ѽ\r\$�PIΐ�v(\r�q�'l`T�e�MlQ!�qN�\"���C�eD�2���Q��'b-`�;� P�\r�%�y%�c P��Y%�`����V�m'�#'R���'�s%0f�� BrgC\0I_\"F'r���4>ҶNMF\rcN(Ip�G\r�B���,2Ƥ��/��-R�0�������.x#-RԒ��N�k6\"C��vȳ��\n��^�0��+C1�_)�2��\n�JXb���OXK�0�X�M�.�ȳE5?�b�M�\0��R�k�/C�_�d*(X/�\".��S|?%L!b��F4�I(\"��3�4��7����q8L,�0Xdl�bH�B�ր�J��j�PETֆ��ϱ h�lS�w\"PBd�\n2\n���Z�~�Un�G}8Мy�;�@�A���v��z��-lB]��Pǎ��>-�#f��2Bw��=��=�j�Cפ�\$+�>0�# ­f�/�\nnkV[�7#GT;�1�P%hD��\\�oGm��puH60���Cfa�o��^*ׁO4����I�.�t�7��Q�L�L1f70�S-��-�%,��1�df�'+\"�I�J��bJn�n�B�8npnɿ\0��\0ǔACl �-ƈ�,�ƮVd��N�:ƌ�t,/d9E#\"�k�2\"�3���t�ڌ'��P\\�g88�gK����.����*�(�~\r\$�	�N ���f��R��	\0t	��@�\n`";
            break;
        case 'cs':$f = "%���(�e8̆*d�l7��q��ra�N�Cy��o9�D�	��m��\r�5h�v7�����e6Mf�l�����TLJs!H�t	P�e�ON�Y�0��cA��n8������d:��VH���+T�ت���X\nb�c7eH��a1M��̈�d�N���A���^/J��{�H���L�lP���DܮZe2b��cl�u:Do��\r��bʻ�P��.7��Dn�[6j1F��7�����761T7r���{�āE3i����Ǔ^0�b���穦p@c4{�2\"&�\0��cr�!*�\r(�\$B��%�k�:�CP艨�z�=	��1�c(�(�R�99*�^�F!�Ac���~�()�L��H=c(!\r)���<ia�R�B8�7����4�B���B�`�5�k��<���<����񌣒n99�Z�BD�Fo��\0B�4��B9����*MC�������I�� ��l�4��H��h�L�\rx��[f��!\0�2ÐLb�~���0z\r��8a�^���\\0�4�B��x�9��r9��\0��J8|;%�A\"���1���^0���0n=EC{��P#��5���7�CkH77�L��^w��޶l�:��[�\\d+0}�P�(�S0��b����c*:.o :�(�\n�\0%�	��F P��\"\"L>9���Ŋ�z�^�d�\r�z��W@�:��\\������HW�QCX�&#H�4!�#A*C�FM�BbNȈ������@W�Zt�.z' ����LB��&%̹[�@V�2��3�:-��{4���;�#Ӝ(^���?6<�����\r�Е/-C;O0���@�6��X�<w/wJ��v���bW�����r=�Å1�H9�����\r��=;RQ3G%�?��ĳC�0���6	I�<3���	�%?�3�8V�.�@Z(h\"\$%��2� �O�4�*��^���ա�~��n6���X�I�\0\r��@XHs����A'��v�ȑ���`a`놄�\0�3�`0����B�!l@�\$F�80�M��~����\"}\"!��s(���,0�Lߐ�(nZ��)�:��\\�²V��\\+�x��kXa�b�p^y��@�\0|��Hs�U����g��5��\$	6L�F��«\$B}!�t�Ed +P�MA�x:4Rz�\$�%��UI�4Y\$��U��8�5j��ʻW��_����*\\7I�ܴ��ɝ\"�/#���G�(��uX`�.��<��QBL��s�ҙ��w�l��*IAnʘ:��LM��8\0�a��i�pBR�*iJ��Q+�J\"'�<v�2ܕ\n�����Iy/s����ZC_�����H��8\n\\�j\$~\0P	@RS�I�����BCSb\r	�F�C�(g�����愐��ɢ��+'u�te�Q�*PlWP�H6�3D�tU���8�y�]\nA	̉���O�*�f���L�\0C\naH#�@�C)�&OIl� �`��h�ȑ �\"\"\"A��RLg	SB�j8^�I=	�,��i坧�Y%:|�q��0����.��=`dD�I�'4�5,)>ߑ��D�ٴ�,�^�����sxB�O\naQח�fΔk�\\�	�YI\0�{F(hx3��N��ƥ�/�8_(V'	rָ'O��M�����\"*E�Ͱ\r���\0�)�\$����I_Ȓj���!���S�������#BFRC�T��ҚD��!���,V�dY\r<'c�\\�_BR`��T��LCb�J�ם����si�t���@��\n�f�P�CdY�AF�^S��kBuTy�SY��B��av��f���Ӿt�?�G�EhZH1<��ڗOk�9b����z��S�J>X��2#����~��!<1Y��I�IQ�CN	�<1aL��BX_�XO|��� ɞ\\�*�N�Y���)\"\$�])�lMT�j�&A�y�vQ�Q�6ka�)9��F�.�LAh2 ���Ai�?���n'%�x�FW Wq1LK�ߺt;�xl`��tӔ���]�54��\nw�i\nq1�6P��ѸP�_��o�3Ln�<2�ts����3���L��F=`� �@�BH)����4T���?,x�H��#�Y�E���S�}��^�[!���Jh�&���!=Y��̣h1����L��]_ۈ��f�E����nu\r����|\0����}=߱��q��O~xb6�(�痻����:Bd̪��\\���=�-�\nc��!��;W���sոg8}h�~Ƕ{F'�D�1k�xm�֠�'��������1�����_Q�z�j�\\���̉�,D��^	E��p�G�J�ɭ�kȔ�Q>�G:I|��=�l]%�sK`�@��N)6\$�j��8�2#,��7����a#~��~lt�s�4�C~y�^����B*A�br&|P8&�p&kփhaPH�\r�]��^�j�r�K�ok>:�H�ph3L�`#LJ\n7�l��zg��F��\$��͐7��-@�^��4�r�\0�����LL���!P��D�h��LM�*	����JφRS��*嬚aL}\r����A,��j�.b�\n��B\rFuQ v�'��p�1%0�g�tO-M��\"���\"	5��\nqY���g�{�A�H-�\"̶& ��ނqF�o��:�NK�b<���ш^�\r�8�q�k��р\"���j�N�s-�����Pߡ�QQ��đ��lR8����\r��Q�q;	@�\$M���/�~�B �\"dl����%�P�|B�\"g��`�ZK��U�*oh?#\n(�&H9��i�gîV\$�E�\r��7�tq\n�&�ֲ�L4�Tñ�q��>a����ޣ�c�\$FJd�8j��pY)����v���q��&e�	'�_(F�nX��_�w*p|0�Z��uQ�r�,B7���Hs��7��.\r+�7/2��g���:=�X@FN	b�[\n��̤<؆�<�KFH1y��,��2��3k1�2�s+�-���@�&�9��5q2��`��c{5F�9�g/�M6�\r��>20/�T\ndD&�?im*P{3s�\rs�Z��9��9�pB��*'ӊ&sY7.PS�;Ý��9R�e�*��0Y.�\\eS�В4��v�31=�>��ћ;E0��3H�3M(�>�b�d��E�,�� .,� ��ƣJ\$NA�)��+S[;b�A�/n�t<M�\\G2�/�tMĳ<�%=\$�2�'�-�)F�mFtA\"��5��SxFG8{\"yĸs#г�@���q�n�JI�J�؏7d��\"4�4t�h��5�\0�\"6=0o��g#�J�#JH:���F1��Kq�5br4Q�O�L�>\"5	O�3���^ą�\r�V;�tl���c�vU��M*b?�zS�D��\$�N�c�K�f��%��\n���p��0�j�%�R �POwW@�6-eL5�Z�qWme��R�!�\\PIn\\���	4�1��/��FI�\"	b'B�K�\$��K�&B���N�\\c�4-|�^;\nL�ϣ_&^&��+��AcPI\0�',Z�{a���o1�yc@-��C�kb�vSlăD�cV&�O'`�%���cvI34'evGH	<�s5�dG&\r�5&�\\��9�I��A�P�#P��w_c��Bsi��% d �&)�\"\"z\r��1k�\"�6h\$�Pí�C��m@�\"�g�vU�igc\r��,�:�\"���r���,�h�amc?e�cƒ�s�8��O��1�^��p �";
            break;
        case 'da':$f = "%���(�u7��I��:�\r��	�f4���i��s4�N���2l��\"�ц�9��Ü,�r	Nd(�2e7��L�o7�C���\0(`1ƃQ��p9gC�9�GCy�o9L�q��\n\$���	)��36M�e#)��7���6�遹�NXZQ�6D��L7+��dt���D�� 0�\\�A��Ηk�6G2ٶCy�@f�0�a��s�܁[1�����Z7bm��8r����GS8(�n5���z߯�47c�No2��-�\"p܈ә��2#nӸ�\0ص%��0�h���&i��'#�z��(�!BrF�OKB7���L2B��.C+��0��2���b5��,h��.ۀ:#��<��0�����-��܃\r��5c�	�2�\n	�\$�\r�&����6��@��>O���#�1�)��4�\\���H�4\r�D0�O��9�`@P�Bd3����t��4\"�B�-8^�������xD��jЅ�����\$�x��|���(���X+(�\$V:�c���d3J���H�;�Vm����x�<�M�\n�&��(J2�2�7=�u��Xމ� P� �א��L7\0 �Q\"�1�W�\"�0�:����uzSW�P��Y#Ml�7ځ��u�����;-��ͦ��r�aCcP�BbC?^�Z64�>h\$2c\$�b����������\"���k,�5ڤ*�'�v��24��+�V�懲@P��F�&�0ʏbk��;`���+���	#k�9%Q���l>����x�!E;]���**�®*p�q�Zu�r#(�\r�mf�4C��\"��4�x�3hj^j��@�7Ē��!��:��`A1������0��:stS��aJ7,��d�*P荊�C&���Gq;6�5<��C>4���Q�M#I���/L�t��PUH��j�\rʨ@B�º#iS���P�ñg�໚ Z��\r�r�\$�CI#���%��IH�D�\n���T{�R�YL)��;�ʅQ�74��Ua�7�\$lJ}� ��Y��']YD�{]m)	Y�8�ӬM�����܁�l�R��<�٠p'��g˹�&���.��S{�T��ϼ��S�%�!͎��gi-�p���p�&��J�G�HC�%����@@P��L��\n	�)���ŁL�`�j؅G5�IQ!�\r-�2�5��O��MŸ��r�F¨g,ȿ���`�)H�����Q�a�P����G��Հn\n	B�u��h\r!�4D�Ԥ�I��-,:�Y�4��g4A)� �K!(0\\�M�PD|5�p�DI��3ᘙ��XoI-��6�.�`�A\$荄�]9~]	��̈́��I�qb(\$3rH��܊.�����n|��s�TVS��ɫ�t���C8��|+�М�zj\rk�֓�Q(Ɂ2�e4�XJ��gYp���WCY�wH�E�K_Si�� I\$0T\n��7�_��r�i ��f,�c���ʀ�T�	�#	�8P�T���@�-�J�0Q���o�5'�ʾċqݷ��s	S\r�rlܫ�B�@F\rD�'��o�nuǩ��RlNy@��钧�����OM����ƞ�ss\r��5�[����'Dլ��|�NEh�IQ*�c��U׆r���*\r����1\n���X�F\rdw:!�=\"�vޞrø,��@\r��mu-��\$�WB�Zn/��`H�0Y�N����1f�4a�Rߑ	��#!����of횳��͆2>�242;.���ZpqG�A�*����Q�Iv.��[�|>�4бbЭ\0PRPO��I&�E�m���\$|� T!\$\nuhO��!t�8Z\0Ń(j3��Y�K��K����b�1J]��X�[�C�ͭ�𖢵y�!��&��C��		z�K�E����E#S�]��)j-(-E}��N\r͋.�wW������	`�Y�\rͮC&�׻��E�j������\"Dȫ�����8ד[M=\$��K(�B�eHI\$�K��B\\Y6�e�@��莡-���m���Š��b^Y��������I�kF����^m\nI�o\rx��~|B��H��CO��Zyy��F�s��g�̿T[=X�t#/��!��)WDB��\n�Ŀ\0��P�b�\r�?F\$\n�&�F����QI��u.������l8�\0V.L�'[f�|:��M��J[Ϣ���\\F߄��m�}릵M�y�SwK�H�Mķ�A:��\nm����w�=߶�)g�}�̴���xov�����c�0��7˂?4�O��K����#e�ɖ��Q�������O��3�~��z��/%X.8ʼ�Ƣ�����D@�c���\$�HE}��3�l�L.��gO��̚\r�K\0��QC��b,�za\"�&C�3�\"(B�tG\\��<��6/\"9d�&f�Ȭ�ͬ�\0ixbE��φ�k�X����:��OX��h[f-\n��\\�0����p����0�J�	��#l�!`�\n�Tч�\rj��E �PM\nI%�/�na���C�.�D/�M0��\"��O��P�ﴛ0�������Pr4ì�b�Yd]\rG{�l�Z?C�N���\"�YF���\rd\ns�\n�t(qR�n��O�\n��i�kk�	q^^��0�D��/|��\"5O!\rPˀ���D>B�.���D����̱��)<a��Q�{Ѩ���BT���p������8WE�++��{��E��Q��D1�l�E�B\rTIq������;��.�h\0�3c\\�Ɇ'��D,�1�h\r��.mV��>�\$�e��k�BJ�%є	e�%)��@ �&�t[�گ��%�(Bq(����E`�`�@��\"�o�H��5.��ƌ�VJ\n��\n���J%�#m��-�)Bp.�ݒ�1�g.+�������(.g\nA��\"f��>C|�Dz@s8\0Zhr�����cN(S/��&�9���\n��0�\0��K<}��	f!pPa��F���o�k��j�j+@���D&�3f�+�Sr2�()z4F�	7�6.���9���cD�BL2DBOi9�6�t�fH�k�!�0\0�\r��m�\$g�8B?=&h���3�\"��%��>úl�-0\0��dp\"ڹl�/��&DC'�l0��7�J4 (+�l[��9\0����ҹ1O��4�\$i��ɠ@-j@��P!@�";
            break;
        case 'de':$f = "%���(�o1�\r�!�� ;��C	��i���9��	��M��Q4�x4�L&���:����X�g90��4��@i9�S�\nI5��eL��n4�N�A\0(`1ƃQ��p9�&� ��>9�M��(�e���)��V\n%���⡄�e6[�`���r��b��Q�fa�\$W����n9�ԇCіIg/���* )jFQ`��M9�4x��� 0�·Y]�r�g�xL�S�ڸ���@w�ŎB����x�(6�n�Bh:K�C%��-|i���z9#A:����W��7/�X�7=�p@##kx䣩��*�P��@���ȳ�L����9�Cx䩰�Rfʡ�k��1Cˆ����:��)J\0�ߨH�Љ\$��������6(��R[�74ã�!,l��	�+8�CX#��x�-.�+	ƣ�3,q��=�#(,���6�)p츰�th����@;�C�o��&\r�:�PQF�;O[ ��j�9��.^C-sH-���3 cꁴ�\$\r�B�������hx0�.\0��CD�8a�^��H\\�SK��z��	5��LcC�\$��4D�Gh��|��\r�����\"�:C�t:�p:����;����K���`%&�K(�2�*=B�?\r̘J��C�,�a:� �BV�`�r�1Los�ƃx[p\rn[C\nփG.(H҂��l�A}�H�\$�kt4�ԸΜ�:+�/n8�ⱘ:�1&�ժ�K��~,�>�76��\n2��ف#�����,/�5��N/�����3�3�N��-��&�^Y��~ô\"�4��\\�	�QS��XNϭ}\n\\�-���*T5-X\$���\n߽(��D}���:-����� V�8��X��,�lb7�Mǝ�4\rz|\"�N��pm'�ꨉ �=e���K�7�e6�#�6!�eH�)�zZ�l�7�@�pn�Yӵr��&'��7��\\i\r'�����ƍ	�n!���2Tp	o<��]\0`'� ���q`�TJPL9AW��`�d0y?�A�L%\rН�?c\0��\0�.Ġk\rr�z\$�L,�H��B4E���v�C*�X+\rb�u��Z�Sk<9-܈njdIl��C���\r&5��WB�3�)��p�R/�t���kZ�;����ѱ`��0�T��(H��)(�����z4��\"�Y)fG0\\�#�eZM�������>v����w}\"A�[5�JQ�Դ��i�H�Q�0�J��B6���5�H��t224���@P�L�Lc19̣7(�)3bX�d\\nd<;���Ϭ\$��EC��EM'�lCdd�H������<�y�6��:O���2�JߑN2���ׄ��F�ʔ���M ((������0a��X�hfݿ����q\n\nQ� T��0&S���Կ(��!��p�8��I E�j8�D�\\��k�\$�2h֪�P��HJ7���%��Rt��\$aL)`@y�;��^Vp�`��'�q�R��i�a�	���f��\$䤕�TL��hHd�ꠇ���a�o���C����1%�����q{&�܆c�ު��h�>.�0@GM��F�K�B@SI�A�jOG�I�O\naP�'�Yg8\r������>�����z��{����ɥ n��F(q���M��o�d�5�������%���W.&�\n�@��2 N�Y@�\$O@傫�Gp\\��fl�o���2�[ːN�@\"�rI4�0\"����aLic-`(4���cS�-�&`�+`	��c&�J���r\$�cOq�l\n�:8:̎���BH�u� Hq�8ȓ>!:@�����)zgQ�<`(+p�_��S�T���N�\n��3��!��i��^�H�z�Z�Ѹ\r��Z��oKN<��/4�Sg�p!:� )��i��0�!�H>�͞4Q�t��^]����73��{�ʈ��i*Zq�f�W��̲�0��\n�!Ϳ�4��@P����c��dr!,\r�U�&�Xc\rd��&�A�\n�ѧ�6M����m���wT��@a�J�4���b*�M�*�z4Sy�*~flt�O��q�����Aa\"�+�Z*\\�;y\nN�p�A�:xQ<J�z{qd ���\"��eb&i�~�u�� �FF��̢��FK��K�;���t��n��;a��p�T#jLC�pm�F�ԗ�g��o��\0PƠZ�s�����<�!�.���C{�#~���'f�Ql�u�lr����\0��&��\$�#�',\"Fs�S�q����v!���8�ס�������0'/��d��l�\" ���R� ꘊ}�f+9��tG��������:)��1n�Dc\0��OD�Hx��\\�-\$�('�,���\0�d��2�p\n�g�}BH��p�.��̏㔔���\$�g(�gb�'�|mƢ=�\r�-��Gl�z�R��=T���	#j�+-��H4#�@D��m�Z\n�T	��ˌ��P:�,�\0'4U�:H��@c�-N��B�O\r�Bp;��t�y/��\"�3����Q���\rpB?�Uc�\nNrGeg���F����B��\$`�u����W1:A��\"}/�.\nù\$�%��\r�j\r�n!Qr0wF�\r�P�҄H\n&0 ZI @G�ߌ�!��o�LK�-��E�/CZ�@ڟ.�@��Ʈжr)�y�W�x�r��q�<���o�\$�}�  ���\0�m�L���L��B��_���?ϝ#0��:ڑwB`w.z�rG\$\n�%\"�#P�%�+%Q��d&�Uy&r_\$��dF=�@�����z��o�+�.q�90�2�ҧ��EDIұ\$p�q�iL��\n\r/(��'F�,�Q'�,\r�:��.�D�`�2.\$jB�g�<O �a��#F�E�3��0bR��1�c0S	1�؞�%1���0�S=*��00'��.�L�#�%o�Hz��5��2��m1�m6�\"s�w�8�2V�1|��::s)#�58d\r&��9�@@�S9��4�*I�q,�%�\n�d�8��\$��Pa9�Έ��:L\$��C����Ss��C�\n��EE��d2�.I�\r���J,����\0�@�/lB����E�/�;B'z4�YN�F��^��`�~S^6Đ0�����\"d�bG�D\n���p4��G�ʠ����P:%,tG�C1��j�dI)`�pYb2A+�\$T�.�#Rؐ�k#�Ǿ��ԽL�!��N�TdT�:�H��5F\"<\$��[Ԕ^=\0�!�^���KJrL��b@�:Nb�pDu4���)�@\r�\r��A�SP\\��N!(*F�6���!�\n�QcQ��;��\0ȇ��R�LеBV�%5KS�_ͨd�'�9S��-`c3�2�p�B{\n��X�L�7�ܡ���#2�j>����Ge4�R�23��\"VN͢5%�����\n�Ru*\"�Rމ<EC,ue^�D|ke�Sn��q�H�K�_H��s52��;I�@d���#�b!�FI��,`";
            break;
        case 'el':$f = "%���)��g-�Vr���g/��x�\"�Z�А�z��g�cL�K=�[��Qe�����D���X���ŢJ�r͜��F�1�z#@����C��f+���Y.�S��D,Z�O�.DS�\nlΜ/��*���	��D�+9YX��f�a��d3\rF�q�����ck[��)>�Hj�!�uq�����*?#B�W�e<�\$��]b��^2���n����>���z<� ��T��M5'Q+�^rJ�U�)q�s+4,e�r���5���-���3J7�g?g+�1�]_C�Fx|�-U����tLꢻ)9n�?O+�����;)�����I�j����t��P#���0\nQ!�s��'�\n|W+������I�Hs��H<?5�RP�9�~�%�3���ٞG(-�4C�OT\n�p�7\r�����7�I��0���0�c(@2\r�(�K�:���9@�;�\"�P#�K[�Dr�())JN�O1~�+LR0�=�8��*��ªqt�.�:�M�c�δ�izb��m\n�������:���ĺ��Q�n�����Ir\"MUq�љĤ ��E>FH	�>�!�dh�����ӷkA�F�v%��P�Q�wK�j�O�zިOT:gE�[��4�L��]DӃh���T�Ar,�֍��	z�]�j�h�2��N)�u�w.�Jb�6�#t�5ͳ|�9γ�@0�c�9�#>g5̓4�5�H�4\r��0���=ϡ`@i@�2���D4���9�Ax^;�pÓ�2�]0�x�7�V�9�xD��l�5e#4�6̃H�7�x�6+��]���|�\"��ᇬ1A^œ��E�z/{&��D���TFͼ|�/t^�'<��	C�[�j3W�(1;�r|�PH�+�#�ݜ��(�C��2�>7��Mإ�\$����^�d�����=�b\$��}��V�v5���ʍæ)�v�6��ޥ��۔F̊\"���\$ap�\n'|h�e��]y�(�A���M�)�:�E��\"3�2vfE'/�hL�0�G�M*2.�\n��7�8�!�\\,\\��=J\$Ԓu`i�[�!°�1(fP�{�L(��\r��~K���d@�Q�uG�ϠgB�ȳ	Y��ՕX����UG��Ć�IA{��R�L#a��f5������ZH�%�҅q��(����\"Fq�Hr9#�~D�����x���� BzQ�w�W���8D\r!�3��Y`K��V���,��e,73����鉍F-�����A�9(�a�2b���ȍLX�1�^sP�l���Se�|.q^���Q��4\n�H���#R�@�נ��\\&�)M9�v>�\\d���Ʃ9`�q#��Ȓ���\"�̢)� ��:؝�&w9��]������j�G]>�i��^��De1��P�6+��B��Ex�3��-}�,l@aJɽ� @Y�ngL��2��Ӄ�\r!���\0@Ԛ�Vk\ri�5���d�͜96���d���� }\\[�Rp.��tC�	\\\nX�Q\$.ӨzD��%%���HME.�E>�a��\"Ž�.B���;�\"�`�ڋSj�]������c����6��.�侮��HN�2��B�1u�'�TJ��aʔ��w�\0!��(J\\/��Q���4LxL�G%:�+�u�\$0|��WY��A�~���g�(���53J���A\r���&���jhm�0�g�S��uf��3\\xge8��@��T��@�1�:���a\r�\0g,� D�	���k������1R�|�*B�N������+P�z�=e��\0�\0(,����B��Q�)^%P�(�.-ɰCo�1�:���{IA�;�\\�[IN�U�\0���Y�n5\$b �(��H*]zLC޺2��iu�&����6oi0�3IOI�`�Ce��`\\?1 a����e?&��2*`�� �FaL�œ\$�{Xs#Ā��2-d�g���cr�\n��REs\$�R�-�%��>�h#]��1'r�e\0KT�P���\\*\r�Ar�?^�G�&q-|*f\0��c�UǑ UF�U�	���^��*���ƦOP�-bKԦ3��h@xS\n��)K3Ld��(b�=�����ݪK�����`����&,�d[�,���:�iu�[��BDg�b��0�K-�Ao�mVD���2�\$*e�OD�#s����C��\ry���liy.�x��ӯi`\n�8\r����A�A\"��]	�,B��!�K�rN����8	����>U|#���������k�I�H�0��E��9R9���^����0w##&7u��XtO�w��T�b�\$���A�?��\\��#k�1�����}�Q�����O�oז�a�.�_����{�qT��Yȓ#\$'�t����P��,��X7�(!����L�B6�o�\nZ����x2�u�t��^(!�:�*̜��zK�q�|�R\$�W���h�~�O��܇'fPh��x�����d�\"��k����=�n�^�w��8a����'(�A��G�\\���/�t�,�0���\r(u�;(�a��:�ދh�%���\n\$pba�P~���>U	ċ�X��rJ<����N����w�!dH�ì*�@����\rb\nf�L��� �hf#�ȁJ~��9*}�X]�`�c�Xc�l\"�*���Q��@���eN�wд�#\"#gge����%�a�[�9���0&E,_�Й� �\n��`�\0���\r\$�ff�����&bG�OǤHΰ��-�0X��7g��@G��b�)!Xy��!��u\"� d� �,Sè�E�5�,��4��0%.����?��\"%D(��� B]\"�/#(�ק�.#��\$D.�H&02���%.'!�����~��b+��3�&�μ5&�H�ɨT�+&&\0_g��2�'�'RA%�q#2�#U+�&2�\$��#�Z�j��L]E\0���2�&0 ��#�>��t|ζ-��!k,�dr���p�=~*��\"ˎ��0����\$�,y��OPC�b+�Ԡ���J�P� �Cy.���Y/��n�.�X�R�)h~-��Ϫ@��v�h��!�(�����\"+�-NT(1�z���b-�����{:���6�l*M��\"�IM�W,�wd��\\E2w҄��>s�#�����WbE�?s�~3�H�Ҡ�F9#�&���)>���?��,b1B�:�\$8�12c�?�?�<vP�#�8Z1��#�@� �X^i9EI<v��UJ!\rg(��wIʝ/��R�C��K��T�~�|��-�<��cF�L��\"H�P�H�-'dLq�(Ʃ��E�MC�(�����9L�\0 Ը^OWK%���4�5?TPW-wOn��E�\"R�O�6� \\�jSE�EteQ�QiDP�MU\"5ҵ:�qJ�R����U�K��Q�C4�Fr^A�H�ͯM\$\rD�3C�j(R�3�s�,�L�9.1'��'�P�=!rW(�p���W��W��-ҏXAkX�]\$��(u�9B��u6��\$�Q.���&Z!_>�x��ESH1 Q++�)\\�=�R�d<�iD��U�-3�\\�7]\"'T�1U4c_�mLS�zjj�nl�1\n��>U8�Q�=+�B�%)��P�,��:�=M�~��9�<�콳�/,�� �M��0ރ>&�,+���d/E\\V\\��f�Hc�7����F6\n���sE��cV}����	�O�ބ�E�Y�\r�O7�b���A?�wm'M\rU��T�_��r�܉B�Q�aR�W3�n��u⹴:2�O��CuXU�HW���m�_�9nV�!�1o�QFs��Dk��At�8�KQ��q�-tH�^�/ⒾI�?�|ŪA2�.T�)�2Q�Їs5��2GD�v��{i7!o�a��z��i4�sK�V�={q�W�E�gp6�u;uGZ�����\0TY}�/JP�C+ID7q/\0	7k+��!%�vVt�kO���ł��p1C\$#-���~\$vT��8��1cB��)C7#o7����N�.�AS�UW�A89���w3��UuF��V�k��8?W�����Xc��g�E���4[TG{7=��0s����Orxu}����X�I-}7�%ۋ�A@֫�����Mt����1W�*�M�����L�xώxՎ��KlJ�Q����WQY`�GԵ�W�}�,���Vw�rb���E���~y3�9.<�Y�]T���?�Lt�B[��2b�ѫt(8��������n��T)���]#Sm�msyC_�w�����Ņ�X��kϙb0 Ѡ.��5�s<�ZJ���4�*A]�.Y;n'*x�;2S�?\n'֧�+��+�9�5��Y�F8�Ju��gN�o�C%����[2�#��?��A�]%�gX�M�U�����}\\r:��1�����`�\r�A�#�R��~Թq��{�'{���#�:��3��Z��`��A���xv��\n���p&�I%U����B4d���A����\$��@�M��)⾼G�\r�s/�0�Et+Ei�xyоF�9E�՞C�X#�������氼�jyRac�/��B��B�әI���dt��\"�83�Nd	�\"���m�q֖~h���0�/&)Z�L�7b-;E�{��߭&��b1r ��X�p�4�=3�3���o�a��j��Wo��c%a�ɺ?I΂��4�!��A۶A8�^;�([�����	�r����p�����S�����t�Ü� ��Հ�A�Q�b%l�\"����h%\"aG�h���WZ�\\)�fǫ��z�b.=iz�L,p��iz R�SS����6��k�0���؃�0�s/T�(д䮘HΜ��c8�v��(�4�䁓��S� \r��K���g���O�#�v�<�.�j����Sn��<[%�]F�4\$` ";
            break;
        case 'es':$f = "%���(�oNb���i1�I�r:NP�`�4c4N��p(���L,5� ��P�\"��\$N?!��abS�\nJE9͎R	 �2̎gC,�@\nFC1��l7AL%�\0�0�L�S��~\n7M�:8(�r4��Fd�J������%&�̆�1��L�+\"	�oX˕.�M�Q��v�����\$g�^�S���L��awȔ��u�k�@���4 <�f�q�ո��pq�q�߼�n�1Cu1����9*`(�����ȭ�vI���@���U7��{є�e9IH��	�����J�: �b/��;��\"J(-�\0�ϭ�`@:�0��0�	�@6/̂򇸍Jx��)���C�Hҥ��1�]\r���*A8#B�0�B�`P��\r.x��7�+�2����V��҆�i�\n���Z.�B㛑\$�L����35�:\n)��+�ݽ��!�����H��\r|n&\r���7�ۆ�S�c # ڵ>���\r�\$�8@���1�Hz֏C��ԉ��H��\$9�p\\������D4���9�Ax^;؁t�I�ϰ\\���{\$���2�x\r�z&��zT�8��^0��(�� ���k���L�t��è�:,����:6ҩw��0��i 	èʜ�l�����ҵ��(�Cʘ���4�H#x�:��,���1���L�H�1��TΏU�R`�8`P�2�c��˦x��@�:��M���L���k4�e��9��� 	��\$�&��A�;�(&<����C�/)��������g�C�� �=<%Nj)�\"`l��T��7�\n���]7q�%��8S�!���̌�Z��%T1?͔\n(4�2�\$��J,��-؊<@�G82��܀\"I0��H�_���\$_��i�A�菚��>r��L�р���v�3��&7��3�d���F�j\n���ޗ#s��LT�B3g��\nZ��J'0�p�Z�� c�!V�PP�I%N��!\"溒Q`�a��d��(T3�ą��6� R+ �!eCɩV�u\\+�x���X��c)%(~Vb�\r������A�E8ŭq.H\$B��z0�{A�,�Й#^���!��p��t���3\0_�aZ(1i�\0�Lb�[��l�U��Xkc����j�z�ID5���I�D�}�Z5Uf�T��>������:v�t��eUHZ !o���B	��i@�T��;(a���Ջ��P+�V/�����^�J�6-h0�S�N�i.�`Ǚ� άQ ǅ�!�=*����,(��A{�5�,�S(��d4��'�qL�Tl�8:�Nr�c�!h� 2=-�xw9D�:��Ayq�\0K@�Aj8g��^}����%P@�V��ppU�1F�.�	 ���^1�'I�aC��I��R�P;�s�p \n�(64Hd�)���e�(;.��'��P3\\����8ƹ���bEf�0��/I�T��uIBn,�t̞�%	\$<��:��*�,'4��C�\nZ,��C�F�<R�t��R�P��xS\n����TC�x 1oԚԔ�чL���מ�+�`��2YT�<��)cQ��(�J�)�z5�!�@�\\A�	�Y�U@��約Qّ�(�5�,!����E\\�Hl��4Ao���a!�@�B`IA)^�@�xR\nWл��c5�d����\0���&H��s�\\��dA<8[�J�Y�7f���W�?��5�NyPٻr4V��F��H��,�<\\O\"ȕ<�7S�o�[�M�AιQ��+۳�z~��.�³,y��4���/D�IH��(�ƶ�le�M9g9<�ō��z�~]�I.쐥��o<�\n��7���ٜ*;����%��Ӷxni��(j9\$L���M%�@\r(e�]�W|��jT]ʯ�~�� k���3��z�r�!���gȝ�H�X0�A]ޔ8Z�5��^�Tjsd���{�\rל��	6�[)�.�� e���S���(��nM�F���l7CK��!P*^��p5/�����(Y&>�؎�PԎ�j�8����:�{x�X7��/��C!:����nFMr�N����GZx.-G�q��O!�\\_�<�M��%1�,�ǹ��ba��#�i4�('�[�9r�O/�0h����yWJl�%����^0����'j��H ,����YF��|�v���AT=�1�s3�FSr\0�'��:�ȷ���N	�ZN5��Р�\$	7f7�Ô]�F��R/�\$��<6��d�D����\n;U.�(��lr�1�F�5�R9�9H\n���l�觤��y�\0�r\r%����F���,��7�_!L������şU��u3�r��1�zhF����(p�������W����q���H���?��j\rD0j.S���e��/�J�8�\0lɌm�\"����'RMϔ.o�L��C\$=K��\"KZ�';�/���<xG��6�Tя��'Gz�p\\	�FwN�Z�Nd�f��|�H�pvᮢ���p5,]e�i�0j(�+�E�sϱ\n�q\n�7�mp��H5o�	�B�FJD\$<-�X/C�l-`P	@/C���fe�/pB�j�\\)h�CH8�R!%\"i�\rK��-JL0��p������\"�bR��:\r��f�>�L����� ԧ1,2٬�dp�ӆ���lp���:f�},�\0#\"L�&g/�'�%Ϡ`�\\�\r�0\\�ML#q��D�1�nf\\��p-D��##��\"�\$�g/#��\"ʫ���PX�-��'0j�-%m����s�BF�� .ɱ�;q���i+�!�rnfN��GLʙ�����Q��R:aN�O�\$r>C��ϱ ð�S'x}�`�\"�B��M%%�!d`Ҁׄt\riH!i@� ��-�AĂؒ���B.���2��ң)(�s�\$��g�*\$������'#����B2V���,��B1�q�r�.\r���#r���\0�Be�9��I2�=�ġ�i�%0�%m\$	�\rtU���J�i܃vc�\"�\0]ʐ��F�j0ƳD�+��\0c�BL�2.�wS:b�pt1�\n��3l��̤NO\n0d�\r�V�c��L.B-�!/`I�\$��u�,C�\n���Z8c-bKj�.��3Il\\\"��5NvK���Ǭ,�+d���/t�����=�<�J�ڇLܒ��P]����TP��[,?�Hc��\"`PĀFf~&s��C�J�\rfωI��S��c�\"e��\"�/I�`�0��ҝ)��o�/��0Z/Ԃ\"B3��+G��Rl�)�x!c�b�H�0r솼\"�GE�L�vml m̄uL��C�0��(\nI0���S�	�\n���d�'I��]cv�\"�?�v\$6�vIm�7oZ�O>�G�OGNrPB�1�O�1\0޻��.\r&���\$5�L>�:[&d�-F���	\0t	��@�\n`";
            break;
        case 'et':$f = "%���(�a4�\r\"��e9�&!��i7D|<@va�b�Q�\\\n&�Mg9�2 3B!G3���u9��2�	��ap�I��d��C��f4����(�a��L�A�0d2�ࣤ4�i�F�<b&l&+\r\n�BQ(ԉD��a�'8���9�\rfu��p�N�I9d�u'hѸ����&S<@@tϝN�h�g��P�9NI�9�;|)�@j��jC�,@m��\"��ٳq���|�����F�=ZqF�̶�`�*��y㹸@e9�Rr!�eX�\r�l���#���8+��/��H:��Zh�,��\$4��k�§C�|�7����[־Hē�è�1-i���5N�;:*�-\"��#H�Kp�9B�B9\ra\0P���<��B8�7��走\n�0�)x��Q� ���>�\"�x��H����H���.1���>H��2��:\n&\r�j���P��¹*�+�2;�@�?�[�8@/��1�h�X�\r�X��\0�4���p��|4C(��C@�:�t�㽄\"���-C8_\n��#W�v\r�R4��P��\r#x��|��ț��R�'8j+|ܦ�Z��-j��2h�����\"��죷͒��䚭`P������5�P�!���΂���>��8�A���@��ˊ��\n��*��4�1����\r��h�B��\"V�뜣�^*%�L\0�^IdՌ��z�H�Bx�L��}�j��D�k�#,�r\nH�90�y��cH�5��J;n�X�64�<9�lc(��h���@P�v�f��-h(��7-�b�\r���3��z��\$��^xm\0��8Zm=�<�5�cha��bq�#l��B*Wݭ8@��+��\"LG�H��'��\r�4��S���PϪ9{C(�֍�w����Ȍ�1!�Cx�3!H��@�iH68��㱃φ�C��S�������U�W��3���F	ҋ#�2���\nW!���)-Cq7\$���F�JɃ�'h F��kx����ʵV��]��~�Vņd����әd�yh�蒷��\\F�O-ռ�Or9�0��d-���S\n� e\$�ܩ%���A�����\\�U�:W\n�^+倰��ą�f\"h����|KEi���Cـt�@��%S^����R6|�f1P��f�'��z�@�!��Q�+[T!�%��N���l�\$�Wf5�p�@#�l��Tyd�Ҩ j԰3�Iӽ(e����C!�0)���8G���!f�}��@P��QF�r���A\ro�i\$�p4&�Ҙ��NL ?��������\re�Z�Ҷ�WI|���k��ށ�=ƽk�� ��lq�1���[Թ��i��sj�\n�(��!�0���=�q�h���e��ŶK����e�P�\$hCHy�/q�F��թ���Nl��L��G�)KC3�J�1�{�c�al�RT��9�a+��d�(�j��im��B��D�r����d�E��#��,�d#a�8������.���*��a��)�B`h���*O%�']�`����%B	9:E\$)�S�E�D�(A��P�(�kl���P�*V�w� E	��VPf�m	0�ĮB�wnMЂ�RJQ�s7�T��퐁\"q�5(����a��=ކU��qA�BWф�D��du����r�!����w.BHB��~a���a���A�j�V�_B�����6dQ��9N\r9o�2�rs���y-Y0��q;01�1R��H(U�Չ�]��]�r�	���+?��d>Z\"����S����7a��\rL�vD4��h�(wH����b�Lj<LNl��x��{nEc-��&����.N���B�g�[d�}�S�M5Pkm���WE��9%�o�����%@�B8G�I(8qxK��H��5a���69Mfp�L�CS+��+P��P�R���pWO1�7�*�sD���V�t�p␵���D!�J-���@�k�<��2CL)8V���Z�;��d��j�L�GCĝ�l	19�h��v�*�,	�q�sRWy��e[���E����������\"�S���5V�8�p�,#bËgV��Ѓ!� �`肐4�g,L�@e����?���iM�ֳ9��zb'0E��Lӕ�.s�\"�w����^d�O���%�S�͐��أ��Ӈ�!&!��Qa������,���	�z<P�AG_~�Է�m�`6W�^ʅ'`�q&VM�-�'bYe�!���\" ��Kxd�^S't���cP�h������@+HDkq��=ް�_3��n�4�^2��nf�	ό�ln+V�~�o^/�O����!�VRL|��s�AO�\$�ćD^'J!o�H<�F�,�FP��,6��t�\\h�8�\"�gI�~��r,�����ʹ�m�{�������6��\$|�h\"�!j�˜!pz��\"�d�\n�j��N�+6�C�p/�v/��\n���\$MPp|�.5�ZF�XWC8>��&.�D��J[djf��|�d�e��V���:�to�v.iV���F؛�,�2TDG�Ap��M%�[p�\n�P0Et���c\0��>e�\\��IE�`\0S\rÏ\r��B4_��`1yPb�qoQu�n��ƥ򪑔`#� ���P`�\0�Ð�E*��N��x�\"��p�\r�;	�n�����q�' P܍�\n�Ԍ	�B�ct	(a�����C�8���!�\n\ntېyP +���r\$�lN^���\r+	\"r:{����҂`i��i�s�#��x,h��=RZ[�ڌ��\$���(1��\"�}Q��\0�(љa)g9RB�rTȑbr�eD����#/x���\$�%'C(h�'r�� ��c3\0��B�&��v/���2�'2ދ&r���.��2��n�5�/g�����N֦JI2�+��Ml}oi�J5�`�I�2S)��%N]1��2��rk3o��s:\r��\r�ل�GQ��Kc�+�����Q2���t�\$��N�\0�֢Λ�P	�k`�ٲ|a��s,*��'\$��'��9�\\+���;k;�wS��\0���e�\0��>�5	ӸbF(G�H�3R@Q��#��(Pd�\r�V\rbJan,\"|��?��K�\n���pz�f\"��\$�A�!n#J���N�^��P�P�\0���`�ۀ�\0�.�qz<���Q#�9m�\r'�E�V�'>Lf\"�	��knh!û�Bb��]���Nhl,�s���p������b�����Ĳ-f��������.�ivD�~!T��T�r17/�zBr3C90�\r��d�.��CK�j_3j��+�4�>M��� B���'X�f�\0\n�\$ES���d>�\"j�@�\n-uRj�\"�P-\0�[Į#�ObH���O�m��L�2���4�0MM�]N�\r����5��/�p0��\r�:���8y�ț��g`.\0�	\0t	��@�\n`";
            break;
        case 'fa':$f = "%���)��l)�\n���@�T6P��D&چ,\"��0@�@�c��\$}\rl,�\n�B�\\\n	Nd(z�	m*[\n�l=N�CM�K(�~B���%�	2ID6����MB����\0Sm`ێ,�k6�Ѷ�m��kv�ᶹ�![v��M@���2�ka>\nl+�2H���#0\n�]SP�U!uxd)cZ�\"%zB1���C2���o\r��*u\\�o1���g��{-P��s���W㤵�>�--��#J��K����<�֋T��s��F��T����/\nS0��&�>l��`Q\r{US!\\8(�7\rcp�;��\0�9Cx䗈��0�C�2� �2�a: ���8AP��	c�2)d\"���rԢŒ>_%,r��6N\"|�%m�T\$͊S%��楨�J>B�M[&�%ES��<���H�PW;��'ﲲZ%n��S�,����+>�'.r%!�����R�@��ȩbҥ��ҡ���'�,��2Ϣ8�N\$#������F��0�Ғ���Ъ�@X�O,���P�2\r�\\\n���7��@0�c09�c=o\n�Є%\n�H�4\r�80����`@Y�@�2���D4���9�Ax^;܁p�VU�\\3��(��ր�2�d\r�T(���P�\r#x��})ǒ�����CHIAh��HS,��s��H3\$̻8�~Ƒ	#\0Q%��<��^\n��7W�(J2<n�T���SB?9+�2K�ʨ�L�Z�)�����3T����D��%�D�2嶘��H�Q,O/��,�k���J,��/E���M\r�/,��.��j\n����+b�MyjV�.��2�E˭���<�8�ڟ��y2oA*(p�(��*��Ll��3�#��u-�'����N���X�dK�-�-��'⭦0�Н߇�S1s��a���%���������3���w�Y̐�P�!x�7�ǸZ!�\r#� 6B��A@�w���?��C�t]��\$r��! (9@�`�B!∐B�^�0tf|���Uj�Ft��� H9w%��r<�Lh�\$b���6C�**!N��1��ˁp	PЉ3��u�,(P��5X6 ؅���8j+�Q=�\$Z1~k�K������h�zd�\0@�P_��Ux{�U��� �CHdU�P-e�����\\�ru̺#��K�w�,���]��Iv\0{#)e2%fn�\n-6�0���=��G<J\r�� �_M��R\$dE�C�g(��ƨjB��.!ꐨ�7쎤\$�[+mn��¸�*�h.�ڻ�\r�M�v�JkF\$vP�����e��P�	b���2=�Q�,G�����<�b1!<����rnK�hhB��L� @�C`l�\n�heg�3@(�ê�Wa�:Ѡ��9��4���!�k� �&Cl���;4#'�R=O��9RtuY\\O'䅿�C�H�P	BS��P��0��L S��N*�B����	xC`Q�1ɘ�z�A��?@�H�j!C/�\r��FP\"JjU��(�dF(*&�(�_�:�C+09�5t)�7��8,���2��4�:j�C:ܣhR�0��-q\naH#S`��t%6'��É�J�S\$��J�rj\\��	>�p�R��(�\$�Շ���;�b�4��>������q�G],�<	\$<��+ l*Ba��,{�ÈuCp3 ��d����1��'M�bDu��f��n�2�TǨ�F\r�Ʌ�\\�Ǯ����H�NtF&�c�Z94H��.DQ�i��F�`�Z0h'����I\\�L�d����bC�:��!*U˖܈c�Q�v'���I�ը�����\"Ka0��I7%�N*!�\n	�8P�T��:@�.z �L�Φ�P4ϔ�6�%�����b��9*����J��0�RC�5����>SԱ�y0�D]3� TñyxcT�*��^�vI���Hj{��oo��N��ֳĩ��>]��up\"S5���\nr7.(�]d���g��\n����F!��v�G������3Y�H{�bE0�d�[��)�����#cM�m�k�D^v�G�B��\\|]��6���Ym��`e�~��`�7O��3�Z����!|��b�EK�\n8:�ۙ�>`�\"��%'�\"b�a���5������-,�\r�W@K�u�hn}۽��aTA����֯00��.Z�K�́{/������K�!P �0�0\n\r(a[�{�\r�]�ӜRnC��Q�X3�^W!��!��*y���FǓ�dX�d��2��s�Ign���kӫG�Ǐ�o�M�MT���Q{�=e��b|=�\\�\"j���(��j���Ǖ��|�2�����4+���}��*�����-'W�>��~l��,4>�e���ꛬJ.lw/�'�Kmb�r����Nd�yͰL�V���C��T��\$��6ȍ���*C-�e-\n���Cd�Q*9#�8Cp��s�N�\r��PH�08B�2L�P���y�\$&�|���\0000D��Z�c\$4ВM�njІV\"��0|�g��.f'NX�)v��R��n�f��\$��g\n�\0m���j��709͑'��-�3p�-dS���&X?pS��E<:��ǐ`x&.��/�_x�Pg�H�1\"�q�6��!��{,��\r�b��062oR�'xk� �&�ߑX�b�Tk�\"�g��e��Q����_��]09������5:B�\"�*;�D|G8��rte6���d�(6�0�Q���b\$�t�X#�6i��*������w0^3\n�ǐQ��2I�a!��!�~��]�,İZw���\"�5\"Q �?\$,J{�<��PS�NtF1>��Rd�N*���j?A�\n\n)�C��qV8r2���#��j5)'}���rxnp��<�)��%\n͙�*��%Q\nI���y��O�:�n�\r�,1��2���2I&9\$��!�\$���R��⢼�'\$��0��*&C#�0����,.��`֐�\$�k�����PS*�s0C*8�*���43.��H������> ؤ�v�@�����0`�Ȳk�/r�8��2�1�S8ο02���D>D�ղ7%��,�q.b_;%)K�-l�Q���ӓ\$ /0BQ�N��z-ZJ����`:gJ{&z�ͰN�b1��\$T����%(q\"zR1z+�?CzJ�a�\n61d�ON�'BQ�B��#�������°!��@�l��.�)�T\$(�.8q���\n���p�b̍�^�-�{iG���Kh�=#�}�����%��)n�lDV���8c@��(/F�O�{L��P{D��?\"��4]C�,Sl��B�E*!AT�����B�N��Kϰ0���ChQ�kb�c��Ȅoh�)�_gR��Ϻ�*��\$�Qm�S���1.u=T�B�#z��).�,\$� :r3�xMXm�VF�T�qV'ʉd����\r��_\r�8\"�&r5�����3\0�2�GD\"UZ���.�l�P� sK��'SASQ>��a\"Ss�ƭ�8��Y��9�]�]G]��\r��@��hPR���Ip�M��+�7�<3`";
            break;
        case 'fi':$f = "%���(�i2�\r�3��� 2�Dcy��6b�Hy��l;M��l��e�gS���n�G�gC��@t�B���\\�� 7��2�	��a��R,#!��j6� �|��=��NF��t<�\rL5 *>k:��+d��nbQé��j0�I�Y��a\r';e���HmjIIN_}��\"F�=\0�k2�f�۩��4Ʃ&�å�na��p0i��݈*mM�qza��͸C^�m��6��>�����㞄�;n7F�,p�x(Ea��\\\"F\n%�:�iP�n:l�ن��h�A��7���*b��n���%#��\rCz8��\nZ��#Sl:�c��٨���&��0�p*R'�(�B�J�m�@0��@����L7E�^ԥ���+G	�#��zJ:%�#����`�#�N	K`�!���\n�B��K��JI ҕ#�\$�;���<��`2�P���I��<�c�\\5�3�D���� ��C�93I�\rM�'���&Hز&,	!`@���~�M\0�G�4C(��CBh8a�^��\\�Q˘\\7�C8^����J;� ^(a�ض\r�`x�!�R+#�;�#l@�'�λ@݌�S�!�r�5�2������^ر��\r&��\\[O:����x�:�8f�M�U�*2���II+�+�-�æ��`��\n��H�	p�7h���i�X ��=j3�LY=ݬ3?B#�P�\n��N�9�+��Mb�*���q?`����Ұ�v�i���d�d�5�X�T(��j{�!�s��#�cm%��2�Nl=ݼ��6�İ�;��?s���1{>���Sܠ�\"�	�*�� �[�ÁN�f�ů��AMil�5�^���Ta��;7w�#���S̻�TYl�YS���4�2���	=+\0:����#H��o(�ҥ\"�79/{E}CN��7P�2j�P)r���2z�S�Y��~�\$���.�� \\LS�&x����(n|�=������1a��(����3��2�҄�`��F��0�[�zpi!\"�C�iAh��7�{�:���(ŊK�b�\"��X�5j��ʻW��`�u��K�rX�%e��^B��6���kP�\rq-DT����j�#�[kt���p|	��\r���>`�����&Б�s��r��7����^�M�\"DMA�V��]Ex���X�f-���C��\r�h��5�q�#��;�Z��xd\"GU)�S�bګ\0O�;/45c�\r��m���vT��9���5�^�Ù�~Gtê�d�a�.���XnM`*��/�n����˨L�ɓWr�g�������@��<k���e\$wa��@\$&�м��@����#\$'��J��@U�e.D�R*h\r��!��M�,&Fb�2���3&�o�S�I�D�Pe��r����RP͜�(���PI��,C�Ц��6U?OO��9��0��0-i�q#�\\al?��3@���&Q!�vK��I&�ܒ/c��Ѯ\$������!�_L��;�g\n���O���པ�JuQ,(�6���lB�|�����ܛ\$��\0� -\\� RC)Bܹ�5˔������=����Hg%�U���<BހV����(K�0\nm���rvL0T\n\0�h&��1 ����<H	\$\$Dr��6H����aD��|Wp \n�@\"�p@ �&\\�C	ů`���ZX�9�H8q5\"ֲAB	au쾣TuIk�2���.3X�6�SӃ|��0��<�I��(�J:jT�ق�on)�9���Qj+:��Od�N��#,-��+ӻ�t6��Z\"ujs�x�\nu���SC�-����eZrLsa����B�8��8s4���'K�,��?꜑dy\"^�э��I/E��8���8ܛCs_�)|��_����15ɯ��λun-5\$L�tc��x\ng�1����N{5���G�^�RL���h���4���rA��0h87���\n�zW7\$y]\"��.�'�g<��C	\r�%k1qc1h2j�@ ���a5����pL�T�s�����\"X����@�Z�\r�(i����4i�|	|x<�qȕ8o䜿�/�L1n��M��i��6䥉/Nˉ�:�<;��!�̬�4�!� ާ͸ݼ�ڒ�K�_��Lw�'��:��X��1_�Jݲd%�E�o�P�ػq&!�,��+-X\$�B��ʑi=�	���J��z-�|((\ri�O�?:��PD\"<ʉaA ���pZh	��gøf���\n���%ཪ��P��}��f]s(�d�jR��=�]������Oп���M~a�&u5��CIvse�Ȋ0���`(e�'��%e*q\\��+��﷉B�E��X^����/���R�#Nc �cd�&��	�J(�_O��.��|SF�Ɍ:���k�\n�� ʏ���̃PG\0000*Aɯhd�p�)�/���>�C��0`���4I���|�O���F�NhbNn����B�U0��K	N6�\0�פH�h��\\�O�r���M���P�0�� ��i*��n�d'�n@���Jb�Q\"�3\$\n,��q\$�.8~�c�@�M�Bj\$���@��FK�T�\r��8S�L=��c0�6p����L�dr/�O-r1L\\&&,�D^�w�-B�cUl�\0qs�vd��2'�;h݉��{-�-�L�O��p��Z5�����	�@�� ���� ��NBT.�����>�>~\r�)��W�{q�\rM	�m1�0G\0\0�f�l9Q��!+���!'�Б�!�\"h��2P�6\nf�\$)V`y��\$@�\$��ɒ\n%RF.��ϡ� �%rdCCs%�4 �@\"P(���(pI	�^o(rzsR��M��0�L6�W\"%-���*�c*��h��+r�+��,\0A,B`m�> �%1ʑ @�r��͠��%�{R��S `�ͣ�_m�/���M�0���k1����vI�5ҋd2Ģ�q�3S*�.5P���c��� eܫ�\$z\"L�&#�``.PV�sh��4#�d��oΊF�r��7��0�a��@(J�nU9��9�:)�D�\r�V6�x�+��Pr�l2�ht��Jd�Q\$\n��� p��'G�\n�LRPj�.���0��l��dR(�&��&f�^T�BŃ�0��L��A�X\$&��5�T;��`خ��w�#�k�1+�S�T����Jd�Um��b����['hɂ�-Cl������CLiG�d����F|`�z/�G31�t�D �'	�I�XFQ�^�F\$��ơ2\r����8k ��\0��%�\n\\�`,�\nN61�f\"�J\"H\"�h��#n��TMf�N�OGCwN���e0<L6�e79t��8J��H�#��F�%R�djsd�Щs�M�\"";
            break;
        case 'fr':$f = "%���(�m8�g3I��e�A��t2�����c4c\"�Q0� :M&���x�c�C)�;��f�S�F %9���ȄzA\"�O�q��o:��0�,�X\nFC1��l7AL4T`�-;T&�8̦�(2�D�Q��4E�&zd�A:�Φ脦�\$&�̆��fn9��',vn�G3��Rt��Bp��v2���62S�'I�\$�6�N��\r@ 5T#V����M�K��xrr�B��@c7�i�Xȃ%�:{=_S�L����\n|�Tn�s\r<���3�6΄��3��P�����\"�L�n�����7;�N15��h��#s\$����88!(�V֣p��7���F���P�2��Z��\$�\r�;C�(��2 (\n��)�`�E�p�6�L�\n\"(ê���(c@�a��\"\n!/�L�\nL��0��P��I쒜�B��8C��V�ʲ�)�.q�T73�2�6�l9ϴK���dXP�T2C0�\n��˴�J����\r@�-Z2�0�hʿ��j� �\"ҵ\$����A��`�B��9�}4�2OH\"���N42�0z\r\r��9�Ax^;܁p�X�n�,3��(��C�,2��`��\"cp̋%�@��|�/�p��ŔT����a�;�����^ılj5���1�Hؓ��x'*#(��#c\$���P��v6h��� @7�h������ ��6C5|�<K�~h�KbL�9�A6�}B8�\"�e-Dh���?Q���!���:3��;4�Ϭ��)���M*)��y�\\Σ��l�H��!vnj&\"��^&;0�2�D|�/���)�vlU�ƨ�N�8�~��eT�)�\"b��N���>�vI�#p�-dS8�����A��2��h'���� ��E]]�a�׌\nR�>\"��8�����ֹM^!����V6ۚ����xY�D&�A���P��}w��PϙfFx�%�w��\rX��I�i���B�R0��!�\"@T��&�0<���o�qP5	����l�U�#\0��`�\n�*G��B�6a��r0���I�[���\"3��C�s�P��`@S�^\r���/�>C� +��-DC	�Z��lU����\\K�;�h�H�Z�]�%�.�D�a�?��@��J	�\r�E���i��qR�S���-M�,�T���'G�m�H�c���}p�5ʹ��K�w.�R�^5��FԔ�V�(6��@a#�a��(���Uk6g�a(\rS�\r0��b	 C7IZu*����x���\r��ذ 'v&��ւd�٨h���bnM��X�B%BÓ1�\n�)�::�Fe��;�9!�\0���\"�,�iJQ�d\$7ӐII=�+ի�j��Y�/�l�r����z=(�NJUO&bt0b�\$������&���CԆKQD�5��ְ\0i���|:�(#�t���@��p aL)dT�)4:����m7�L5G�֨�J\$WJRV��(Cl)K� ���\"d����s��N��١�H���J\"liM<6Wk��Ѓ������\$�8#m�rZ���W\r���/�Q\"lxS\n����I�a	�T��j�Yn������9e������6Ö́\\�Gd� 	���U&\n�ћcP��o\\�@�7rM�9�v�1��\0�)Aց��+#��N�4����E���͉���,�Π�=#!����h�xNT(@�.0�A\"����ZO{4奴܃f\\�e48T�J�N�	���)���lY�=�x�\0�B�O��f済%�՗�9�d\$��S�~��xhZ�W�d]�{f�:^hSY�t�|�E5%�c}`M�j=�/s�r~CG<L�I`�!�M��\\\\S� �L϶�^�\"*�\0�2�I���&b�@%F�f{%����D5a@Mj|&�t�o���L�먶|�K������tSoC`�9�����)�:Y���\r	ܲ����R�r\n�y�@��CC�3{'�[�6cf*S\0���UYV���')��B�K��6<�֕��c\ryz��M�V*%��X����r��<%Y����N�˗%T��ՙb\$;(3����}�K�U��N���f߅@�BHC���w�·:(%��\"Onz[�h��\0���C9�h���&\$�m�VG��vK����Z��N�CJ��*������-��+�P��l�7��e��\r���o`�Q\$h��¤��?m�3����OG�;\n=�^#�����ta:>���(��K\r�����|	�	��\"�ɔ.Q�LRR�x,�p�d���D7�j�����tp���t�_!ń�JI�t������bk1�X�t2����� ���\"�����#���j��*q`��Fbfi����m�qJ\"�	�kBO�S�\0�BK�\$)���*=\$��pu���H�c�?o�\$�`#�?pB��t�p^~Pd݃�k�I̐uFP�ŞO��!�B��m&j\np.\"��.�����c����f����f��E	�1��P~�F�B��ߦ�?�:��*ϧ�C�w�	nxJ����X0U)�jQX��{���mЁ��j�-��z�PC-\rBK�PBq�%�\"g�\$�U\r�:a@e�|��pV�f���\"#n���`�OQy/L��:����\$M��.��#C���r����N)q9�!.đ�qg�S�8;�LDTm��Š��\$��0�p&�k(�*Cr%��e�0�ʮ4�ތǢ߄|V�-l�A�'����a���2mZ��!�P��I��K��.!Ɯu�\\�\r�����)#c	�D�1�1(1����(��1(E��b\r&d\$�\$�P��'qI07*�|���(q�Ҩ2�+\r)�[�r貽*��>����-�y��%V]\$�K�iC ��4l�������R�\nR�11eg��3s��-d[-�V_�`M��*�q�s��.r�4D�2��E�=��cD�!dT2�P?�,2M~A�3�1� �x;�}����7���3�HGv3��G�6�:���\"�m+S��;�92�:,;;ơ5N�=N\$K.4��H@ʯ#b�����>\0�>Bm>��&M���/�)?ŉ>��2��@��3ú�.6�.L��R#S�D,�,�P�1,PGC�>q�-D��=KC�>O��o�B�>O�����,ny+�_����(��t��0As�Dp7G��H�C1��\$��?q@��U����jo�ү�g��ӂ�q��4vKD�sLӬ�'�L�3lB;#h�xk�R���L�K�a��1�*��|m�NE.�DPޞ\"�\r�Vŀ���T3����6ӥ�G����Q 2WM�d��P1J�� �\n���p�O%Q���W��T��d���\"��\$.='�m�J~SF�K4F�d�Q�1�Oe��T\$C0U�@D�cq=�d&�6����#E��SR�\\R�f��|�#*j�\$�3c:3��</|C��V����v�H�m_f~�q���k`Pr��\r3C��q�_ME`��3R��S�V��Z�����VJ�*:҂ ��o�����`m�Q��s����f����Gâ�l�A��=-1V�\\�`ʇ*��]�\n/��&E1˸5�)\"�_�:��S�@k�)�G]��&��c���i��8�Pb �\"�,a\0�NkI�q��p#�";
            break;
        case 'gl':$f = "%���(�o7j���s4���Q��9'!�@f4��SI��.��i����Xj�Z<d�H\$RI44�r6�N��\$z ��2�U:��c��@��59���\0(`1ƃQ��p9k38!��u��F#N�\n7��3Su�e7[�ƃ�fb7�eS%\n6\n\$��s�-��]BNFS����� ���z;bsX|67�0�·[����Vp�L>&PG��1��\n9����llh�E��]�PӒ�q��^�k��0�����&u��QT�*��uC�&&9J��Ӑ����: ����@���9�c��2%��#�&:��¸M2��2CI�Y�JP�#�\n���*�4�*��\r��?hҬ\r��!�)��!:����C*p(�����V���҇4��@7(�j6#�ç#�B�`�%�*~Ԩ��������J0\\�6<�Z(��C�o9��+dǊ	�[@�i�@1�@�#\"�@��M��:�64L���0�Ƞ�G��8�A�s��\n43c0z\r��8a�^��] �i�\\���{\0�Ul�JX|6�.��3/)j���x�%�ʉ�c��ү�n ڏz8�J#�d�=�h�ƧM�R�W��\r�B�=�<,�\"q��?B�7.0((J2�8�+���T�6�j�J�v='����#7�O�\rU'�j\nˬ&�)(�G<w�s!x�\r\\�\$#;63�1��lz��qjR�zp9�.~R2�Bd����b��x������D�N����\\�8\"y���`lNOC�☢&U\"�7j��\r7�8!OY�u ��\$܎#��4�l۪9��صt�o-R���n(5\rT_�(�6ǈ����#.޽� ��u/c'���aR\"q2;w���(���t>�\"DO���X�����Q�\nk�B4�\"Tq�\"�L��-j����Ua�ac\nnE\n�0)�)6f�HD~��V�R��?��_�\r������څ\$A�C\nd�j\$oD\$���N�!�4�`�!�@�V(rV���8���V��]��~�C��RG]c,���yE/�n( }���[�|�\$\0ȼ�\"�-��7+x��S�)@7�S�N�@���Nu�2\n��V/�������J�]+�|�ĉ��',���HB�ZNվ9�Rb�>#���\n���ޢ\n<�QD�|P�%�B��I�b�k��@�j�r%P��d���\r��YJxh�X�a�h�%<�1�	�3�y��	\\�=��E��p��`l{o�`��#!=Hh�9t=9�į4�@\$��\0����r��B0!ļ��8�BAC0\$��bD�:J!�m��pާ&o�*.��\0��\n0�i�8�`_c�玅�p�L�b]{r�cM	�R���ekT�j�!�I����\"��^�XK.�xk�a)� ��x��&�P�K�j{���9��G�9�N&,<(��ʚ4F�p��U�嚫�4��C*e ��X�B��2��گ��v���>fҽ��)7\$YVam(�\$]dC�uH��\0�£=%�ŷ�����I��܎���l30������aY -睤¢��W�3�����O�	�Z�@�KIy�#I�u��gVT���&�ay��; ����3���q&b\n2aŒ\nZa*!*P�HZ!J}���P�*\\\0IPB	�H)`��� E	�\n�7��d,�� @�3J�����RXO���cN�ʁ��\r��6^��D�����i��1�P����2�1E�(�rfI�¡4ʖ�xAb�CErnU>9�<�^U������C�z�F��<�:Z�{8'���n�=/6�sN�(Rg��)l�����46�I!�ͷt�/��5�ʚ�k���T�Q�P�u�q0c��1֤�Д<˄Y\"��P�U.�	t�g�'�6���3���\0\nb4�:�J{UO5��� QY:�l_r�`R	˳׷F��F�c\rd���CZ8�F�6��ѐ(s�ME7����|�\n!�3k�B2�]@()*��AB-�ah�����+i�P*`0�)z@\$n7 C:���-!Ȟ��VgL��c@�1�)�\0S\$�0��S�7��3�諨%T�C\n��F��3�p�B��0<t�A�`'GAj��u��UL i���/��׈�(cP�z�z�j�@��ҹ����Fw��KOw��^2��Ad���H���F�㖹�J�9mLi�_?Fr��ЗSpF�I�^{�G(�bD�'�y2v]��z\n-���i�h��sDn:#/L��otZNk^�?.�HK&���d�r�C�G��0��F�?��K����I��v��'�^�D�����\r�oI�\\ݼDe.pE�O��g\$�ӣNI@ʙ��(�\n�LL.Or�lԢd��B�B\r\0��u� ��3�~��\"�Z�cS�vJ�3ͬ{�lm��ů��o�b������\0e�0�XͰ�w����uǶ��{��!�����H��	O�s���E��Ͽ�L�m`M����-������F(�n��N�0�Z��\r�������\n�x�Ð0��\"��/����ķqݫ�c7	q,����dD�k�/N�Gf��G��B��Γ�8��!�`�1+��������Q6̱B�dv��@j��ܤ g�H���\$��ݑ,O�v*�f\r�nw\r��MP�e��bu	e�#Q��{�\$�q\nȣ�|#��q	���D�q���H_����������Cp�'�2�h��@��m�\"<Q�.o�pP�Ю4���ĕpP\$к�\n��Q��R/\$5��#ph��S�q\$�p�R�F�%��#�e\$C�d�2��g�,3�p�2mj��R��Ҕ�m��{)=%����*��&��)2����W1',Pe��%pe+��c��G�ƖH�&��!-�\rr��`ʀb�2���/j��f\\wµ.�N���/2-��M��R_��2.M��2\r�<�52���:���4S%3��d�>d��sjHer�\"�bH�76C6�	\$`���ҒBMĒB��cRLj�\r��'�4.�9#Z���9��c��]�b�O�4O\"�4�'��\$�<�6���~H�J �`Ʀ ƞ��3i7(v�Ĕ1��E6'��t��7%F�|� \n���p�P��*����#�,�1�!����(x�ry+\0G*3�\\cb:�?�TM�v�.���?�2�i9+�^)?C��`�q��*E	Ԁj�@��FF3>&Є�m-\"	*a��'�T�.��b�\n4Ը���DACLMLM[,�L�V�TMClt(b���KT�p47K�w�\njm<�d<4�ZȐƠ�m�p�ԌmU���\"�hQ&�A@�&�\r�zR��.�x1��׃o\r�\$��M����l1��d�J�J1���hĄ��	���j'�OL���`�/C�\\R��L룞`�<M��^j *��";
            break;
        case 'he':$f = "%���)��k���ƺA��A��v�U��k�b*�m������(�]'���mu]2וC!ɘ�2\n�A�B)̅�E\"ш�6\\׎%b1I|�:\n���h5\r��;�*���bJ���u<UBk��0i�]?�F'1eTk�&�����G���~_��&�0�E�A�d��4�U��¤��M�B�����i~��ŕ�\"U �hn2\\+]����[��v�G�b�ҥE������(��ŷMƳq��nNG#y�\\\n\"N���e\r�S���t�N/���c��2<��\$\rC��6�\"��iJ\$�\"�k��'�*V��*Z��9гw3�r�k�(�@��s��5K��%��L�-LR�k��{0ͬ�<Z�\$��\$�3iH�/�4v�-ry���0b>�%�zZ�HiR[��!��1������S3i|ľ�# �4�����������\0�1�o��<���=�s�;�#��7����?���\0yK���3��:����x�W���<<As�3��(��4��2�z\r�+�<�(��\r#x��}�z6�\"���ݡ�RT�#�Ap�+��\$��4ɲ����#J��9\r�B\n��r�!MrO!��B�&�hBH���J�9f��9/��s4(�%z������	|dk��w!��S�#��\$�)�,\$ˋ5�E\n�H�0�P�,ȢZL]�JJ�d�lN�]�6ͧ7R�,H��(��L��7�������mJ{�\"#��\rs�[c7�8�R!�\$�2K�f����\"�z���^�2_8q9C�jI���[t\r�H��\r��z<���sϯCό���]P�p���;\\��C�خƺBHfi*,O��8�-��8�%IzN�8���¢,#�����A�S�4mk��R�B7�\"��T'6c\r8!�{�!�X#�RJ��S��9a��\\��\n�Q�UN�UZ�U��X�7�����W@��:0�U�\"Ą��:��,IP��eJ&�d�?�֙ۉ (l�-��p�sGn'Mt��@��\"�U\n�V*�`�U��V��]��V�lXo����KE �D�!'�MH�<7%�Զ��͞Y:qS�B��f�y�ZH\0T\r�A�u��l\r��1\0�x�Hr\r��~�����s�C`� �`o��@)���sr,5D�!�ט�����f��%H�,�\0P	@�z\nc+�.�(!����nQ!�7���C�is��F)��}\\��\r�ZA��[�3I�6�I	G)���,s�Ù�P��NJ����S+�\0Hc���3�y|'�c!���W�J1,����7�S���9�@�1B��(�g�)��Ľ\nN\$���֍r#!���r̊1'�*&X�U:\rޙ���S��ODIܛ�^G��8!��t7��%x�5It��qDDX[KBO(�-z��O\naR��\"(Y�Ld�&jaU�4E���-b���L�-�i��U9��d�M��`�/	)рۛ�_,cs1ID�9�h\rR'f��t�FSw��L4\rjX�l:��*0J�͂T!\r��\$�Ō;.7s����b(�ӈ+��ɒ�i�]Z��H����[�a��d�UR���̍\$THM��n4�0^;�[��H��l^�r���r!��\$����fb�9�xn��;�Ĝ���or%Լ�d�5�ף���k!Dq�!�BE,ZC]���V�<2\"��^�DWhѽ%�����JK߃�]>��>\n�pD�2�G\"DD����vrѧQ�.���&��I,XBVz�գ^��c\rd!�#�|(e�Vu�8,�y�%Ⱥ���2�r���7|�Ƒ\nZ�G\$Y��vN]Le�f��\0c���N��j�p��C	9ܠ�,J��Rp�ur��k���g�ӌ�md`�'X�H���wV��	o5n�k��EH��.�sv�5J��Z���BK��B}d�����)iz�\r\n��t\\\n#w{6ĸ�ʷ�|9�@���f��:CFU�b2�o�����ݼQ�GӘ� a&�&HX���4�`L�1+����#���aDlJ.[s��m�#^X�e�F6�ۤK\"�s�����%����t3�-k��s��f�I	b���<�0K�ћc���2Zz�Gn�#����-��=�����H�N�/9*b���F�8�����sv/\rڼ�g���x֌�[�u��}[��֦��/y{w9�mϛQ��W|�\\�]�7\\j���+-n7~hB�����d^m-}9��){�t�e�.f�����oq^~X��sۑ/s�S� ��)�{7�ͥLiI��m�J��r��Q��{����Zn�	_޵���r�h�����&�\"_i�1+)�����dFn�\$̮+��䯚��H�>�slvAOb���z�Vm�3��]\"��J���n�c0B����L\"�D�/�����046Pc'�M���BJf�4��y�j��\"CЦ�c������P��-:�o��e��'��M\nP��rhD��-\08��f�k�q͘�n�G�J��\$\"à�/0g.��P)lz��\rB\$K�W8]F�����c#q�Ʉ�L��ÁI�� րcܠ�t>.���	�� �i�V�	�L�C��������B ؒ	K~�l��z�x�F��(�{A Ώ�%�� �J��A�xIQ~8Q����\"\$����d\r�:q�	m:�OR6\n�c�Z�B���¢Zi�`#�������t!z:���^/��-���D:�\0�`V���T8�Ќ.wD�7*�b�p(*B߯�����R���q �\\�Ϭq\r��N�٢����##{\$\r�`�'m!ʊ%�|3D���1FT�C�&�Bn��!C��2�\$DD�*�*`CE�Jæb�p��1v�d<�p�&�#PB���-�l�.+,�O�/�.J}+.�-\n�CT�,�r2�p�,�\$�e�z���r��lĆ,oN�f�\r�b������+v��Zd�\0(�hqHTc͘x�,�D%l;�iR.���2��a�0\"BM\r�x�\r��<@��Vg�d��l:!�.�*��Ӟ!�";
            break;
        case 'hu':$f = "%��k\rBs7�S��N2�DC���3M�F�6e7D�j���D!��i��M����Nl��NFS��K5!J��e�@n��\r�5I��z4��B\0P�b2��a��r\n!OGC|�� ��L5���\n�L��L<�n1�c���*)����)��`�k����56�L�ԁ��:'�T���d���2�ɼ� 4N�Z9�@p9NƓfK�NC\r:&h�D�7�,�� �*m�sw&kL��x�t��l<�7�c������V�Ag�b�=U��\n*GNT�T<�;�1�6B��5��x�73Ð�7�IP�޸o�X�6�*z9�C�����;��\"T����ʑ��R�&�XҧL���l���R��*\n����h\" Ȣ\$�\r##9�E�V��/�Bح�C�a�c�z�*.�6.���51*e,\$H�Z8�x����-�\nձ��2�R��Y�BR4��{{93���\"���=A\0� �m��k��\rI��1�l(��\$t� 1BA\0��\r5L���\0�2�\0x�\r	���C@�:�t�㽜4ґ��8^�����!xD���l�.�46��H�7�x�%B��R�#b/����5��c�')�z��h��˯n���/�&	�a�a�CR'�@P�y�#pη��#Γ��NC���.OD\rC�'�װ��sk��2�8��>��]��6���V�-� �3#�6ר�2�B[d����ì1�2�֕��:v3��>����9V�\0�V�@��z�l �%�pb�P����5+���k�tr�8��0����&W�0��L���E��L�󛃇����\njeЂK�1�G��H�_#-j�u�D��:=�(6-��3�#�ű�#mH��kx�<zxZ��GJjw�� �ܕ7@��g������Ł�\r�{X��C��2�\npܪ�Xs%A0��F�i�4�������H%MYF(��r�4@��!�P��3N)A��?�z�C�#ᄻ�Z��iwg�0R`\n�l�\n���jP5\\��6���V��p\$��~j�X�!e,Ŝւ�!��j�p�ȳ�hK�o�ºX��]����dJA=6���*Uze\r��RjT��1t���?��\\-�tȟ�p\r\$�+�V*�Y+-f���\"�][��@��Ø>z!���\0����2&d���Za!r!�h�#`�cy '��K�A\rUR�\rȬ�r�S�O5p�#��HN)�3D�1�4��b9��,9D3\"ȃf��yT��C3�\$-VfdߚúdA\0c�� 4�Xc��;E�15�����2P����f��{\\P��,p@@P�H�~�PPO�Xs\"\$�!���5Uad5�\$�fD�ɩ�T�{r�J��O�T�\"�Ip\r��*�w��\$huT��fW nM�q\\�dph ��3�c�k*D�i�j�S*iM��aL)bF�[�v��\0�[`ddG��Vp@�O�aCHԕ��^LI��)�p��Oh):)�5�8z�AAO���\"�THy4�u\$��.����'aũ�2z�Y��m�T�׵�\$��e�b��\rj�(�)ɽ �!�ql�N@s�\nUh�brN�HtW�E�4[8�g?�-ېz���7K�h���U\0Ssd���	�0T�Na��,I-�T)�-���\\�2\rm9�P2�j�p\n�у�s��Li���a^�Cxp(�ռu����)t\$8���Ã`lMU��&gJ� �E�'�2}\0PLiv%G�����-�ƙ���N.k�Q\$С'��q\rቨ��S��kW&Me���\r����{��0��u��o7��c�YP�lc@͚��s�OA,YR~�h�P�ż!X��j�\\J�7�Vu���a�\r��2��(�҆h���R�H�b-u{#�¨]ԂFԙ�&Dl�(w!���w�Hn.a�^_�t���Yu� �.ڂ�;��a���m�&��-Gp��c\re�!��zk*�e�G�ضR�*��[S�g�\r��p9���	t˳8r&)&~]�`E�F�<�&���~K��cG�B�T!\$HCI���yP��7K��in��^Pf�t;�d༪2r�3\"���P��s�I�'9J���@�\ncfw����{˟�.�9c����_&)�U�N|�)�B5=0�~e�I�K'�ק�w���Um������:1>�����J�����v�RG��s�ߗs�nmz�9��7w���q�0E�Y��M�)�X��5B��1��:����si��L��(� \0�CҦjp�b~�=?;[x7S�i��m�\0\\P�n�\" ��[��p|p�Ɏ,/	���%�ق:&9G��k��#L_	���jѷ�G8-m��������e_ä�!�?ؖ��&i�����{�z��l駴���#��l\"�Q�#C\"`Bz�Ò0����B~-@�^f�l�m\"j)�@FT���L���]6ì�mz%*.�v��D}\"d��G�r�f.f��� `%k��ǈ�/��0t� ���H�F�I�\n�����@�Р�\0,�0u\nL���b|�vGbw��'fu�0%&n��.?-�hDt@������.��([����61�c%�Z��c�N�����(v�P�ʏS�~�`؟0�\rk��[��l�m�9�vi�~L0CD��έ\"�2��`�u�jgIt\rez\$�|\$r,��fJv��z#�w��)�*�5\r����LN��0O�2�����h�1^ۑ~���ͬ�̏v���.ݧp%�`P�ph�S ��\r�!R\r�;P�\"0�w�� e*��	/�#nԲP�#�E\"P��\"2EL�;�W#��|�	b���	�x\$##(o, �C8B����R=!��!m)�%k��l/%˺a@�\r��Jc� �^r2��rg#'�+��p�l�;�����9%.9BEB�Ҥ̃�!�T0�.r�r'��ҟF�As+M�1r��R/&�>0�)ko2��O�2ѓ,R�;�<h�6����dʪ�~�>!dDM��SP?�TA�FA6-�5h,�~�@���_I�-�����S0�3�S�c���%9*��\n��S��0s(��%\$*jࢆ��O�<���\$�+3:3=s�,�/�?�5'2Q�<��>��Ͳ�5PNmFN\\ð�H��������&�)T�����B�3�	d�@�N#����d.oC��dO��GCXcsE���[N�6�\r�V�^�e�U��\r��������RE�T�ږ�L��\0�\n���p~��I<%Q�ǤCc�ʮh���zt�A�����&O�\"\$\"�,\"G�}B�sD\$��H��!4�ࠤ��C\0�O�0�HO���:��8���J+\$A�/�)� �~�`޼�ħ��;��S��=�7�\0g�`�\0o�:PR��`f~;nb�o>#B-�C�R����c�p�4�38�5�ҕ��b!�X-'X��3���cT5�|A2hHb��n{u�<R\n�f�&��<T�ZaB\re`s ��l	�r��j�'�T\$�Vq2�\n�N����^��?�z�2�@2�\"4	CTa\r�{_�(Ί�Y�Y⢸0�e`-act\0�b�&�\r���b)�0���a8)��@#��b,�Ē3�R-afϥF@�\r�";
            break;
        case 'id':$f = "%���(�i2MbI��tL��9�(g0�#)��a9��D#)��r��c�1���M'�I�>na&�ȀJs!H���\0���Na2)�b2��a��r\n ��2�T�~\n5��f��*@l4���ц�a�\$E8��S4��'	��l�����d�u'c(��oF����e3�h���tƝ\r�y�/s4�a��U�U/�l'��Q�!7n�S>�S���/W���9�5��&n/x\n\$NX)\n3 ��Щx(�6ǝ�ӑ��\"\"C�i�ߚ��yӇ�!9���c\$��9:A*7;�#I0���X��\r��|��iR���(�ڑ+#:>�%�:068!\0�Amh�ɬ�j��BS�;�8�7�QZ�%\"m ���N�}��kZ����(H�)��\"��8m���	�\0�5�R����ڗ�j��6��������>���1���`�3�X�Ǝ�KDâs��?�`@-�@�2���D4���9�Ax^;�ru4\r��\\��z&�o��2�~\r�3���4ƣ���^0����\r(��	���Er�%\n�5+�L��d�����tt���+��sx���\"7?9�X�6G�z���į%	)�w\$H@'�����B%0��b ;@��%׃@#\"�:���Z1��q�����,�M�����X�J�(���Q\n���:��}�i�#.�#ㄖ����4)�\"`1Knb�׵��4�J����\rr+�!���mn��i�%~(2l�/=�BKT�Z�s�%j�h6_���C�~�+�/	�0@��Lj���<B��\n=#5�`RS�\$�h�3�E:�	y��%\n�{!1�!,��s�̀�@Κ�t4V<�(�jv�\n\"���(P9�*:�/Q&�6s���&�eb�:��@ ��H�C|Z5QMF��'J���7�S�CQ��%h7U!��}���p����1/47��X�_u��ג\$�h	B�?��<7eT:�{�9H)%(���wSOUN�u@����q!��*�Vك��]��7�rz�_,a����G]!��-��`�Б�c�����A�!���c,��#�<�. ��S�N��ճ`����}4E\$�@�D�\0c���4�t(Pt#(Ŭ������\n (�`��\$=&\0�����_�\"e�N�ǥ̑�2�`2�&�	a�M�ty*�q�(� ���GU|D �9:;�ph�xn:gܐ(EN��c#I�3�Shc`	����NS\nAC��J�\0K\r&L6�cf�T`3L��T2�C+�W��6��@�J*cAƦrG鰔��5&�@��CY7Bd�\$�0�aɊ�M��FCH�ËA�d��W��T�'�t'�Kq�=�\0�£+D���Ly�!D+�<���b	���E���@�n����\$O�-\0�D�ή�tNW�h2l�0@eKpF\n��(�����~����\$�<�Yk�z�R�OxO	��*�\0�B�EU�@�-qA:3��κk�c2\$�3�X��B!�jL��Hβ��A.���A�:'L�RYKe�#%\n�%f��ת\$JF*�+��i�\$mT�5�?l�j�2'� \0��`˃5ED<�[�;Oԣ|�\0�2����?K�h�����H�`���Z,m�n�=0yj��G2�2J��qB�[��V��8KE�\\FT2�t@�M� Zk���d	� ��6�s�I�zʿ�@���NS��q\$1�`���|�Éq&&�ƶ�v\r'��-���I�RLI����ՑAH�U�c\ra�J��a��.���T!\$\n�ӕ1���'\n�'�PG@���p@rY�\nw@�f%�`���`�L��'�e�3���dk�e1~)%�3�z]R�1e&�y�l��ױ����4�2_�Q��;�4��)�DD�㒶vEiy����g>	}͉�9\$�*Q	f�I(�pݭ#��2 �\"6O�ժ[����>�:\nA���P��h/��k=\$�Z��ZٳEZ�ck�\"Z�_n�RcLV�i�[l4�=�e���/z�ۢ���B�՚�}�����yW�nd��y\"�%����\$�����!��ЈyC�+재���������y/l�i�p]��8�[ĳ2��bK/\$v������Tk�s�1�Lu}9v�إ~]�8D;�}6����x\r��-K��Z��E��й�.��9�'\"�8�c����2R5h�8��`�fu��w<�-Y0�]�{��V/\0��?Y�x�Q��OQs���<��J��!B\"�U�k^��u�i��л�T� 8�IaM��J��ik6����B�X<ee7��o7���'��#b��l�h��;�}������C�dB �>y���xL~D����_���֞r�o��&�-z�/�\nv6�����&�~&�f��-.�v����vǢp�.c0��^�.��4ub(a�2BĒ�B�P4aĦ��w�IDx�h�\"��0B���P��(���_������.bi�/�k�CIo�p��b�� �f�P�_�6�:1\"�#���0G��P�'�%/�@���!p��&�A �`DԗP�ď@�����J\r0����	���00�LZ�LYŴ[�l[O�L����	m����\0�1'.��\n�Ll� �P.�^�#���<��׭R�\$�ft_\$�n\rxڢ�wHt͎�F���DV\r�V���!bȩyBthb��hO�Dʜ�\n�'n\n���Z��ў\"�D�N��#~<�D�DF��fW�J�@���,vf��C�ڢ�;D�C\$��\"���LZ�7*��'��&��X\0ި�ZQkM\"t9�'�HH!CFIO�y�Ln���,\"�gc\$��n�^�RV���\$�4�m�	N�%����(��\r����2���5�*͊Z˩�᠚#X�FR&�9��eNj�ҦHi�v�+|�`꜋'�/���L+-�g`�0I�-%�%\$<;�w%��l��)�#-�@�|\r��&4�db'DNnN+dO�DBR1�";
            break;
        case 'it':$f = "%���(�a9Lfi��t7��S`��i6D�y�A	:��f���L0č0�q���L'9t�%�F#L5@�Js!I�1X�f7e�3��M&FC1��l7AE8Q��o���S|@o���&�dN��&(�fLM7�\r1xX(�-2�dF�}(�u�G�&s��4M\"��v�Z���g�Z-�(���J�.WCa�[���;fʒ 1�N���̧��ƭg<	� �g��J��er�K�DSd�׳&Z���QT�\"���H&�9�:�o�S!�W3�G#�s��ѩ8L�g{A�L�%,BR����P�%��&��J\"t��jh@�e:��H\"=�@�7�c�4�P���B�ʦ�B8�7��f*\r#�&���rI��`N���b������������(��?���\rã�2�#�^7D�`޵#���Ll�2\r�[:� �����#�1�Ȍ*��\"=%/�i(��`@%#CH3��:����x�K���7Ar�3�� _A?o�^'��la�L��3-	���x�\$³�޺��\0�=�%tí�M|�����:+͚�/�K0�Y���.5K��ى� 숢�,�7���/ġ(�Cʚ�Weܥ �x؎��Th�^�jX�K(�\\1����+-�B4���֒\\���Y�\"3�0�@�1��J!�+�ޱ��4J'�N��\$Hr'5;3lX&1<Ic�ME� ����^��Z�9%-]�~�#pȈ�b��,�ﶶ��l=�{�Y���Ē0��Y��l�,��I̻2�	#k�9.N���ˢ�Mkf\"J��#8��)��˚0�%)�>�\\[S�\r謁�q�:[/�)3�\"NX�#��3�0&�zL\"�wҾ��rb�ݳp�eR\"\\���Cwxc�!,�F�8H��6��OT��|m���j���_u�����2������d�-i���=<���#(�3Evd:�)<�<�£\"�Xs\"Š����\0ASi�9(c�K��rP�%E���T��R��L������!�����vGU8>���UnI��4]�M�¡�\nZ7d,9;��F��\$�(�6BCq#?%a��h!�.�`�E?(,���R�aM<�>��t�<\$��:�n!�������>�F䆂b^H�f#����6`SBA)Ĝ�Hd����#�@�Gu]��%y��x��B����\$�H��wϤ�V���FH�Ѝ0F��+�H�nD'qم&���T!�� \n ('��q����v\$�T:��F�	�am�����Q��M��������ŜYb�9dq���\"-�.��6����\r�WH�<�L�e4���H�F��\$&�*��C�\0C\naH#N�JZ\\REE����B[0i51�2ܜ��3��0���ȎP����\"d��i=��:��SHE\"Z���3\"5����T�hDDL��ʽ6�d���	�L*Q�0W�B�7n>����ѤR��C��E��D��0\n�nB\\<�\$3�F�F�)i�2��HF�0T��4�R�����\"�6K�`�Pii�@��0\0�rߙ�O	��*�\0�B�EV�@�-�-��V�e�-������H���a,jd�i�b'ڡ�r�s\rIj�()��a�l�Ӷ!]��|\nI�y��8d����[oW�+5��C��G\r��\0�Bdy�\nn̓����r���_��g0�)?K�.0(�p\ncF��0���s=\n���a��uO�H�#�b;NΝ�eAM����E������4:t���a�\r��\\_�Q\n0�uh���H�ڟ�V�ȿZع2\"\\��J�5`iY���\"@�\0H3dBM�/���V\r7�ft�v`�)��s��2��H7��:RL��&���d�m@T!\$LBJT�dvy�&�2]Y�aSݿh�N�����/�W��-(��P�d���<<�\0��6�1d��Prh�Ap	u��XC=dJu�����3g���)M1���D�)�l�����/�R��u���:��S�v��ֻ�N�=ɹ����&��}��= !������6�#L����L�,�HIA\\2�+)J�~\n�rw�� �I�F�����-�:�h�ߢ!H%v:��(����%��\$P�Bh�̷��������=X�|sK�\\��ƋYh��ҍ�b5��:�f��ieaWل���+��;pD �B~XR{tXמq�^���U����3������O��rn��5\"%�#�P��n�!�R�tc�z���#Q!x���o��|�'��׼ۂl����Y�������;t���:H��?J�1�a����hk��}��L�ۡ�s��Z\$����� �r�g�A�u�Lk��e� �-����>{\n�wԚ�mW�n�zN�#nD�a��!�E.	Xy5��)���x#n\$i@��#C������ӫ>��x����`6�G��Qj\r�����/�ˎ�Ӡ�ɪ�[�!�\"6l�͐�CfXcR��5�\\8/��f�&���\nZf��\r���x:m`P`��Wb\"�(�壃	�ob�	����M�V�F2\"���ξ�������D\r���\$���A����.�\\k��b��㯾�D���	�S01m\nx��Y��Eb�#��#8H�Mh���bQW��CO��0���,,l������GF=�ZG�􎎈E�\"1Fb\$G�t���	p�h�D~F�{�Уv>�n�1y�f˭\\\rh�&e�� ��a�,�є2(.� �*?ѐ���#�SD�9c:̀���@3���'���%�����%	Q2��gG�IFP�.c	��1�=R\n����2I�p� �&dHJp̳����e�/����m4�\r�]r<�R@+�ֲ��6e�}\" .B�/�+�P=c����\"y-�\\&�\"�&�'(�x^2|H'��\$�#�\r�Vc��9<��6�R��(+\0#<��k��\n���pۯ�xbV��~�M���ܐf\$�φ����\\)@~���'8�n}�x\"���2�q.�CJ4�\"2�ېJ�C6\r%X�M�*N\\nܪ`(bV6rd0\"\"0R���/\"ꐭ( �\" ��,�0��X��&�e�BI�7i\$#N��S_7��F�k5s�5�u627�o8S����7d�X��#Pxc�B��[9�J/NL/�pk%ȷ͜gO2c�.�R�-n�� ��s��,.!����g匸+��}6f�\nk�ׂ<�\"��K�D����r�tM&ƈ\$F���6`��DtZ\$�g&�#HZ��b\0#�";
            break;
        case 'ja':$f = "%��:�\$\nq�Ү4�����(b�����*�J��q�T�l�}!M�`�2q\0�RH�P�r\n �� ���b�@%9��8l�q!W�U��*qQ!� d�J\nV,#!��j6� �*�>�P�*i\nfB�w��:�udB��hh�%-�ڐ((b*�T�8�+��P�M������\n�Z0X,\"\rR�J�E�y�@h0���q�@p9NƓa��e9�����a:jΆ�N��a1mE��\"�;JLs9�ZL�[&{	��>�qG���*)A��9\n�\"%�L�3*��T��/W�t��*��[Z;��\0�9Cx�刎�0�mX�7�\r`�:�z8AP��c�29i	��HcE�%q�E�')xZ��J�A�����\$%BS\$\$zBP\$(YB'㚆����\0@��1.�%[����\n���)a*X)D�XK��12�ı0��DĄ�\n3A�JJA��������ZAaT����U�\n� h�[#�)4s��MA!\$<�_�+u\"�'9PW%��'uHQ��lr�j�Hԓ��A�*=Q�UQREʆ�E��Y���p���O*�he�'v`�2\r�H���*����P��1�m��o�\0�p�� Վ�K���M|AC X���9�0z\r��8a�^���\\0ۖ�AC8^2��|>9�1^(��5v��\r�\0�7���^0��)_�E�XS�gI\0�\$�R�Ē`D���h��k��@��^���%\ns�et[��1X\n��7^PJ2��D,�r�G!�������g9+:�E,r���@�1�\$�DsK��#Zn1HN�D�e��B�sWn���Sim̐�d�6M�i�J��*�E��\0#�`��9\r�21�#p)�\"fꄓ��?���aй��N���\\�z��A��9W���\\�U�<�<�6��iaPT���������)��r �ˤj\n���\$���	�>2< �c�qG�\"W�j�\0 AH7At�����������:P�	�7����4�ɄA���cgK!k�a��T\r湘����&��wgz`o�x9�8a�g+xE���n�0Sֱ!�\0CP�M�/�!�w����\n`a��ȸ)�a)�0� Ę�c���>�P��͐� }\$Y�vf���!vE�c�dɻ5BZ'ߪmS�����_�Zk������*&A5B��+@��8��#�l-���\"�بwb�eo���Ǚ����VRChp6���I6���_m���\0��j@�r��>�%		��L������ZG�\\��cq�]rM���\0b5a��)��f��f%��I��Q�h|�3t\rXs3�9�9CHa\r���#d���G���>Tʩ+\n (\"�^�Q�5F������\\IP!��<��E���*)0�Ke�P\rCf�|1�8�n�q�6F��Vغ��8�Yu;���q�=B4s\n�| T8���.6҈k\\טsC�.�S��Cpp�k��0�� c\r�4�vDN(���:Z�.+�~\$\"�!�0��J{X	�l��9D3���u� �H�])e0��x��H���^LHJLGʸ�+� �Đ��}=�~'��{���%�Te�B�R�#�`Hy�a�?WD2�'a�7, 8�Sp�2	\r��<1�Cc�(c�(F��4>m�XP	�L*=����0\$b��� Dq�-ҹ���tO�{���\$k�[��p|t\n�Z����×�\\�Aᑦx���0���� &\0�[�^a*��`i�(v.�}Ñ�Ae�R	a� ��.�S�GP9Eӡ	�8P�T�+9�\0�B`E�M�K�El9Dx�����Gm�q��;���s�\"�`�PG�R-�mM�,��,%����Xom��8uJ�Q.xY�����D��q..Z����u���������UMR��)�>di�Ds��l9�*�(\r�`(YF����v�T0�Q�J�i%c�m�P\n�!bVJ(���͙�Pl��HI|��EA����K��Т��&9��p����A�DU\0�o�<�vp=1�!�e�C�Ch�\r�)�)tsH�J�'/3U�1��F�T�RB�P7�͙36h!�tA]�ǥ@PC��5����9-�a���(W�yu�Qd3�橥q	W���v�מr,�0��/y�\0(�@��M�o�8]X�)�`F��!P �0�/�!B��ծJ8���yPi�A��_Z��!\"�������m�k��/>�Qf3Z�����M|���OdZKiE�	���j�)�0��������c�����I	s���&e��HwIWD�h\r�����%�U�D�\rW迩]�9���\$8m.��vN%F/���~�\n��O�U�\0I�j&�Vݏ�I� {�\nPo<��j�@�()�&��6��4�Ƽ!~-P�H.��\$����l��v!<\$*\0:�2\$k&�k�Z=����dof�k��馮M%���j�������(2Ƒ\n�z�&�0t�!З(�t�f�	O����H��\r��a�N`\"-���P��L�p����nWb�D|�������)D��U�&[-p��s.nI��\r�����hE�6ѧj���V�\n֭V������\r�X-�W&���&\$�֪+����-���lqU�����x��{h}Qw	1��hX�q��иd�\n��푏\r��,�\"��Ⴀ(k������u\"B��������S�<���i.'	�/{ �}/��!З\"�N1���X'��B芲���v!)! 0A1�*P/��bBW	X\$0��j~4\"��њ�n�PqX����-I2)d�:�2h0Rn�k��+�����0�\"p�F�Ђ'�Th�\"��h���p�,��nZRP���9\"�}-��.6=�ow�7/�(?2�z��.�jA�C\$�^��c�,��la��g�:�Wq���3:���0ISB�q��#)^�E\$����Q�z]\na]/3d�q\$ۢ6��lI>3�E��8%\$.������!��Mq��:�:��:�#��;�H���Xȅ�|e RE(R+�S%7�0��=�dR�\$���Ξ<��=�=��?s�P3�?\r?E/@�/.���4� �@\\1��H�.�B�r�T.�I|�T#BcQC��B�\\�AC`�����T\$������-��F��4t�tx��#�F�qscH���{%`L�q�V(��/=��J��3r!4Rm�0`�B\nP�G���~��a0=A~G�28j��l��ċ�~Y�:x�T�1b�<Kh�e�r�jL�ePAuu1�����\nQ�h`\r�V�@�Bj^`�K���\r��Ȏ_C�̠\r��C\n\$���\n���p������9r-N�~�\0R�P�\rMr�Mx\$BH��D9�\\��t9���2-��/8U�1�\"�,b.�lE,w�T#�T|��|>��u�\$!~=C�!\$RۢX%�1�-�j��9��&��x��X��\na�w\r/����A:p�YS�8��.7#P5T,@��	&[�ICp�f������\\��/��S��M�Y�\\van��ihB�.�H3\"Һ�\n����\r��SA���&B0�D��V�2�Sh�T�,`PD���Ĭ/1�q#bnkngpPf�cN\\\r���b8%6��9��pq\0�vc&Mm%J�dMr!\0";
            break;
        case 'ka':$f = "%���)RA�t�5B�������Pt�2'K¢�:R>�����5-%A�(�:<�P�SsE,I5A���d�N����i�=	  ��2�i?��cXM���\"�)�����v���@\nFC1��l7fIɥ	'��\"�1��Ud�J�	���.������e�iJ��\"|:\r]G�R1t�Y��g0<�SW�µ�K�{!��f�����eM�s���'Im&�K������=e��\"�r'����Q+������˿���}��-�����<�^��}nnZ,�:�K<թ�;��SV�\"�z����q=o�۳*#�\0�LD�����ζ�S����:�-JsL�\"���4M�i(N\".�@�9Z�7�����B�Ŵϻ����&��V��l��7RR��r�F\n���K�t�-Y(�˰Kp�D��LΣ*�x�#	�������Sj2S!��R�L,���*�ʐi��DO/����ۊ��j\r�1��Ч��K���(��N�#VJsR��(T�OTS�)HH��E:��1	%i�R�Վ�M%jtf�G�,>�C�*^͵�����LYP��\\�t�6\$�\$��5;���b�6�#p�9J�:'T�t������Y�Roe\\�]J�[����@4C(��C@�:�t��D7��{��x�3��(���9��P��J�|�(W�|�|�M��Qj���x��Tӧ �dwm��N�	=kR�z���M�;cڮ@���C���M��Hh<��M�cJ�rө*m����\"+q(�!�1lmN�Q���/���O�.��U�/�]03�a\$�?V�ͭ��s�Dj�6���j7�ZN�C-�]�%:o���;먿yo�G�ں���(Ḩt=����I������,�	��Bh�!f ���������ĥiE�JV�{5f�о��ꚋtz�ܥ��Bb�-���<u:ގ!?<+�\$L�\$j(��6�T�Us샇�j�f�n���Y�V��S^!�+�q�5��|�I2#%	^�7&�	[�Z�-�,̦�!8mF���X0e�D 7\n�Q4gn�I��Y��(�7�'i�+L>/D��Ⱥe8�h�5sL���l���8�~	�d(](�G1 !4xf�����^1>N���bN+�K%gܕ��W�T\n�6����|CWƄ*fW�� *N	���D�I/�-\n�roa�n}\n���8i�)�vJ���r2qqJ+�z/e�-�2m���a�V�I�_���-��W�%1&�X�clu��F�fk(eL�����pa��|3�v�a[�6�e����o:��*�Mĩ��E=���\r�ɷi�*m�r.�	;m�RIr�W�����S��L�\$�&�M\"\$b��\$�A\r��͚u)g3b�a�1�<�wd��g2�V�Yxeї9����s���9(@>�%eE̔�GM�|=�6q����Co{�@�5DV�\"5:n���� �\n��!S�*�����ł�&o1�T�V����\"��Y8�]����)L>(����R_����Ez57�!b\r%\$�)u&Qӡ&���?��G��wQv���\"n�O��\0����L�_�>mL��k<C��*�뙪��V����+�M׼uw`��6��թ��e[����el�Bd�	:�F�A��\n���ʲ{�u���\rb�\"�Aֵ���ĜNH��E'�(�C\naH#/� �{�#���*��;�����ƺGv&�������B����&�!h�T�g�m+�)G\nX(�'�5����:ީb<J	��`�++�!jƇ��)&�𣿕�;���J �Й��g�	}GV���4��6�md��4��(��\0ĭjAc�+\"bKt�X�=�J,�m!�Y��_9��e\nϹ�@��\n��j%+J�\$ڒ��Jw��s�3M2e5�ON�;�M�҈��`�ͷ�V%\\Q!%�BPQr��+D�3wj��_Z�;�q|�U�du��zB�E�&�.(I��5/l���8�����{�\$���QqI7�BV�j�6���J�%�\\pwJ�r�0ᑄ�)��7Sm�)���yČoD�fb��]\0Ȳ/��=�U�I��g]W�%ʝ��^n��B{����NAn�{`�ySQ�(��7-j^���1ADݐ�m��;�!U�ʙ2�N/!8��L`q�m��޾+��/�t����j�Wg�J����M\n�6��ސ6���f��/n.+�~��4=Ƚ���ș�#|� A�t��]�#%)�unw�\\��&�m��\\���C\r�1��\nج���1�?jL��f���y�9�ѳ�#~3݉���g���i��i25�:~��X�L�����gaτ�-X57S��t��8�~Y?_� 8E4A�\0��\n�� �	�´�#VyI���n�Z+x���bVEn�G\r�m��)`G\0^-�>�Ł\0�4���E\"~�����#(:�f���J�F���lt��>X(���z&0i�og��._��m;�\$��/�����#�|i\\=Ȯ�h�l�D�<��\$�����*?0�8���bJz\rr���=��F¯.BRp�\r���L¶���Op�O�&�h����\0�j�k��K���vߐ��=�k/(k�~��PA�\\+��j����*���np8�.���L�pd4�i	pn�dj�i�(����/�	�z�1�棈��j�NN��R�G��j��j�(<Nn�^��*�Q�v%�ݯ舢t�.t�~�X&��I��;�t(��'\"�0�Xd�-D�z��\"��/З�i 㨭�����L�r(~�!IO�Ӱ��H���L�2<�	����x��\$���2Lq.D�q\r��h�t̅�n�#�_#�#,�ZA1\$5�\0�2cNP��_(b=�K�^�p�*�(�r��̮�n%p��\"�,Һ��Ar:��	+r3)�V⑏	+ÊMi�ݯYr�(	-(�W/T+��D�@(��\n����g{.`Wk%�� &Z���H���,�2i\\��#2l�E��B2P�K,3@�I2�g`Q��\"�_BT\"���d�Qf�\$C�#�j��4Љ\$�?�%Nl+�k3jN�i\0�M#'�(N�\"��	sp�#t�Ѱ���S�v����n�0/�r���7`@�	>�>f�A�3?Ep-S��S�%r�2���S�@��vM8��x��BX�Ї01�&��Qo�'�1��1�4�+	fD.�1t3�D�>~yh6ms�&Ta:�T�o�BTx��ݯ=:�!H%���!�����\\MԐ��X���.k-���e0tr�-Rڔ�2�I��J��K4YQ|L�J�MEG��F�fQt�F���e�+�2��:�Be;L�OK�PT��I�\"�M���q�&��P7R�34�+��ٯ\$|�5'��Qp�R�25uG	��?�UCS3gOP+N�x��Q�iNUG�?�Vq��)��)�'X4W)�M;U�Vuy+�=3�Q�\"��|Od���bfd\0��\\\0�@�\0���&5����u�\\\0�\0E�]��ն\r�[��or������Oy_���XsS�*��L�]D�\n�Ms+EҜ�4Q1cV�4��%Y�.��@O�qC�uFg���B��(?��CTbL��k��Ub�?4e��0#�;A`:�h�r�%ԽJ�M-� �3VW1p�	��eI-.��-��.�Qi�^	Wj	�����gh=k�+.�3/0t�����?��3F�a���`�d�\"��F�|��\"�UM�sg5!9Q8B*q����b�\n��àp���Wz\$�4O�h�Ѯ8�܌��:�L��97I\"��t1=��+meK\\vn�Z&���+ÃBq��q�\$��X���.x���od��L�-��g!�x�4��llޥ��Ӯ`�.��^��{�-}1O)�&���GJ1�:͌�ѿuQ�\\�eppQ;S��pF�@���֔�r�D�����+aT7g�ܑ �v¬�\"��W��V�xxLwu'uX+88�*��9�'�O�FG�Q��\"i��WĻS�|��3��w��{9�؆؉Q's��F��VU&WLX�q�:g�&XB��A6������n��� ����,w��8�xf�Z.,��LB���v�R�=юۧ W��{ �����\r*�S�qS�х�&�al�JQdh�\",";
            break;
        case 'ko':$f = "%��b�\nv�������%Ю�\nq֓N�U����������)ЈT2��;�db4�V:�\0��B��ap�b��Z;���aا�;���O)��C��f4����)؋R;RȘ�V��N:�J\n���\\��Z��KRSȈb2̛H:�k�B��u��Y\r֯h������!a���/\"�]�d�ێ��ri؆�&�XQ]���n:�[##i�.�-(�Y�\nR���O)i����gC#cY��Nw�����	NL��-����\0S0��&�>yZ�P',�l�<V��R\n�p�7\r�����7�IX�0���0�c(@2\r�(�A�@9����DC�09���Ƞ�\$�����aHH����AGE)x�P����v	RX���3bW�#�gaU�D�̸=�\"�V3d� ��b�S��Y���a6�'�0J�I�`��S���A\0�<����7D!`u�j*FRO+9:���e/�T�-�M4��[�Di0�t#Zv��B���k�*u��:�I	�Z�v��(����d�# �����;�1K�Q�1�p�����\0�\r�1\r�H�4\r㭤EC�Y�\0ym���3��:����x���\r�dB0����p_t�c���I�|6���3Bl(4��px�!�WR�5=S �!@v�d�E��\$�:�aB�����/��i;<��\0��k�h:e��ei�U/!NF\"�\$�:�n�@�#�X6e�wZ�-E:�BQ���G�(!LN��p�y�#����:���W���Q+1NH�p��dJ�U����Y@V,Ļ�D?OÈJ�\\�L�b@�VS*δ�`P��\r��,6CC��0��x�&<�R&P<�\\年K�U4MXEQ�V���giRe9��7��{�����UC��O�V�o:�.������;��!�Z�^�̓Rj�M�\0�-/]�q�ʙb���9�t4O�A��#H2�`�eф�v:�P\"�=��\"̒Gh����+3Bj�J�V%H�����o\r������nD�Qkf�`o��9����8a�����b!�\\��0R��|;��A��R,N��-Fԙt�C�\rh}�'��Ϡ X�7 ���8i�%w��ת�_+�~����X�`�Ƀ��^� �tc�(�@�ϣd\$���\nS�lb��	�%�V9���ΰ#J�e�������!\"}a��� ��J�E�x/%轗��_���0)�Asa*BH�(؃	!�8;���C���zw�0�׃�CcA��\"�g������ܘ͢@HB����*���m0�;��4���6;PĆ�r�����2VtL]Q9�����\n�O�Y��Ҟ��B��Ni�5蔀����D�RiSX�(�[��ʁ*iP�6;!C��3�t �|��g�yhD?\0oZTuֹX'�Ъc�HL�ՙ+�5�!���RՋ�-q�`�2�Es1�p�Ch�!�{�\n?L�ӻ�ᖻך�_A\0C\naH#���	��N;�z)%�A2����/�)�FD��)�t��&DЛ�fF�=G���1y��'�������X�GB�\0003��IXI a�r��\\Q\r��ˡ��\\\0sDA���&VM�Aa�(��g\\Uj%a@'�0�y3�\r��BP+\rp��R�2G�M��;��LR�Z:���G�zہApȅ�w�b^�lA\0Sw�h��Rl��P�v�9�L+�\"�\\N\$A/� z�/�]1��s�@��)s�0��[\\ׯp�Ӝ6�yS��|y��)�0M%ל^15��M��NՅ�hN#=��\n*�������۩f�̊�h�:�¿9E��m3^����*��*��n-�`͘IU(���ԩ�?7b:˶G��n�Qz�2هy�#�dڗW=��_�2W�E�&(T{�q�A�X~�/ ��Q�H��b��}���\"��`��z%��v[�v2Q���R�ʹcbd��!��P��\$�[��pbv�o�b����\$��ǵ\r��\n��&����X >A�;��~w����PD��Y���K,��� �P�g�O0�<�F�c(��H�:,��\nk�\nZ��A�τ�N�>��	�C|��Z�����j[X�alE��l�8�\n1P;D[��b�6*Mq��K2�5p��Aa _�P��^W�g\"dQ]�0�Wב�D�w�L`���C�FKS��K��\n�AG�֕����cs�h��0:�TG.�^�\"Q�1sP��+k�|�zK�鉏�-~ӌ�gc�H!�*M3�<���G��*�&aB�Ī���*C)|����~�H��x�7)B%ѻ�z/.)e��ih��|���#Ft���sC����rg*#���qg\niƠ���L���i��S\n=�b,�WMj5h1�*�%*4f��l���2P-\\5nS#f�&��pH����!J{(�����SE(�&M��C=�\$��h/b��r����жp�&,���a\n���mpvipj90���\n'[\0���mHէ�\0+��E\$�Mrد+	�bl��7�|��g��k���4���ؐ��oez��*|\"P�Q\0�qB��F��`��IYg����`!F�c��n��(�TC6��`��44��zqn��̌�Ϣӄ�0_N}�i�J������-�2J�1Ap:�~bjޡ\"\"��lm�2%Æ�\"P�a:QC*J ��Í�g+�u,� �\"������)B���\$����a.W�l� |����k�>N�a�3(rg�M���an'����RS&2G(s�a	�6.�lnbt���\$��:�D\"ih�ǢH\"��b#A66��:.���+Q�\$����(��+����6p�#D�8��(�-�A&��&�.r����0�B@�q�.!����?.�\"�0�δn�ɳ��'rU('@����� ٲ��!8ed�q'��4��R�-��.��R��s+0�:������	C<�Cp�P\$^�N�7�>��oɒ�y7��9s��n����&�Y+37������\ra1�S-��<,S�y3b�<�\$+�5�U=��=i��>��7R�.!`R����6��<r�>t@F�\$Q�=�S%ƫ'�@�C\n,��,��8K�<����;a���:��b҃��s0��d?\"	)�gCB��  �r�m~tb�Ti)��+�<\"AQnh�\r�V��0\rh2Ae�o��CJ�b��\r ̈��%`�\r'p���DK>���\n���p�iK���O<�1�\r��~�� �22r�(#a�צ\\�\r4ʆ���#�0N�hhc�!#)ic\n��Q�^)�4{R5�*�a�'N�m~H�lT�x{!!����@�\$�̵f�bF�(��=��g�rF�r�&7�d�k�\"�d�6<�xR�� �b�P5�Cj\\.BZЕ[�G��-�#Q\n�,C���59�\r���E���[�ֺ��&u�Uky!M�}�drE	M5`�Z��f}�q����xkoB,�d>�P�n��NV�K�T�m*��aj}���U�)\$Z,����%+�����N�Cbngv�%ʬ�&�r�2�\"��t��*��M\$�";
            break;
        case 'lt':$f = "%���(�e8NǓY�@�W�̦á�@f0�M��p(�a5��&�	��s��cb!��i�DS�\n:F�e�)��z���Q�: #!��j6� ����r���T&*���4�AF��i7IgPf\"^� 6M�H��������C	��1Պ��\0N���E\r�:Y7�D�Q�@n�,�h���(:C����@t4L4��:I���'S9��P춛h���b&NqQ��}�H؈PV�u��o���f,k4�9`��\$�g�Ynf�Q.Jb��fM�(�n5�����r�GH���t�=��.� ���9�c��2#�P��;\r38�9a�P�Cbڊ�˱f��i�r�'������5�*���?o�4ߍ��`���*B��� �2�C+��&\n��5�((�2��l����P�0�MB5.�8҄����2����!��,�,��\"�)�#��b��z_ �r��.���\nH�5��\0('M��T�kX�2\r�C�\r�1�p4#�ϤN�@�?���������#�A�xX������D4���9�Ax^;�p�JRÐ\\��z��u��2�h\r�#,��\"H���px�!�: �������\$�S���]���b�c���/��x���2_�>�.+�&�͚��1c\n�����B(�=�t��23�9&?������\r���K�X�(�O갂�#�B	#p��OC�\"���`�(�2�We���(�1�c~x3��F�*�#*����:9B��4��\"I:-�RF��(�BbG�G��#K<�2�ÚH��`�յ���1�#s�(��\0�:I��d?�J�9:�T6罸������ϸ���8h�4L�ا}��d1��k�\"uS��?�Gg2,T��.+8խ9�l,�b*C���q�.�L����y��%�\"`�.1 @�>�O�~	��<.�rErĮ�c�\n\"���\$�e�xf��K.��H�xg0P7��H�˖A�Q*@��ˀo,��[��2rB>��J��|A@s\$t%/�P ���|�B�H�dkDt*C�J	�\$�3�'O��o��)urd��X	b,e��R�'�9h- ��ai\\�����N��](�ϵ�P� aDR\$��÷�Ap yP����WL�#�yD�u�����������LZX+\rb�u���dKEi�W�\\��l[a%�����JF�;x,H�	fA�����	G%X<�sN�H�b_��A���q�*�����)I���]\r����2��7檮4�D�% @���9,!����ܬ���T4���=̻�`���8	0��QK�@\$:��B]�����I�Ӊ��F��:��\nA��)�@�xw��#;�E��0Pp鹺�q�E>�~�B��T�[�83�f�\$���*�بS��T#Ki��'@�!�0���-(�'\"KP��DNċ�&�M�Ѫs��%��GB��0��DI	1(..y:ڗQ[1.�t�#�a TA/%��C^@�ɑ%�y�����A�8�ŧ�2.~lN�J\\�)�F�\\�5D&�Р��ᴐA<)�J�\\�p v��_\$�\ro�]t����*\0�z���Ƙ����Y��Xk��3�1P͖P�CFF�Z�E��9B.gh��R�Vt4���ٴ�D�&s�k��dId���y�q���B�[�i.g4��|\$т�0fQ�Y�['�AX�g������hlpD����1`�a�띓�m۹�����\"�˱�\"�Y:&@��C����J��.C�x���ǆ[IA	--���5\0N���\"�ƛ�& ��[�^U�'���Cz�v!��aB͋A�R��(l\\��M@0���.�\r���7\\�h�t:8\$�%�\r��f�&\0&`�NZ�cޟD1��Eޔ/L��i������� �9R��I���4�f>�úV`����>�hn��\0�fovS~\$�(b�J�IU\$)5���ۤ�CX�\\Ұ���Cv����c���݇��dg�1��|�\n�����(g�aAB�݋�\\J���F.��0D,�@�M�Τ{g�%��v�T\n�!��Ai7���u��J�\\�/�Y�#�NQ�%�!��V.�QӀ����,�w5sJȈqBZl��(5o��L�N�p	\n�(���_ӌ1F�]J�u^��zؾs=t����ȗS&l���[�u\nDn_�sl�^\0S%�v���7A�}����a������^<~�oh\$�wJ�q��^b�pF��-�������85�I�~��N�_r�^�o2E��vش�t�,g܊	s.{f�m�U��2-a%��B�n(lt;9�6m�`�\$§�g����d����?e;�^�����}+=`\\�3y�E�Y��#��a�.@������Ĭ>��*� ��*ºwǊVh��L^�F�wØ��H+D^7Æ^�h\$��;\0�dB�0GdB3\"��B�\"\rp&�\r��bB����*5��\$&�f�\np\$�Q<���̬�x�f�c���	����w	p�h��u�����..OL�\0p~v\ng�\$����\r0�VP�z�]/�\r���,t./k�����N����dJ0���3N���.�Ԑ?�*����L�/��#p�DB%\rί����#ĴL�p��\01E�ޫp������p~\"��I��Ff���d	�\"�~!\"*@B*@�Jp����O�H���1��\$&MɌ-x6JH7��ON3��'�m�cqI�i�\0f���Q�<�d�K�b �Mzؑ\n���J��ί\r�a ��6���B,`��;n&!�g ���*;�4�(5Qn��`o���!�\$��P%����?��,C� �6��Q�F6c�-�\r�K#+m'�-�^�� �2�-�F#�^(/�jƾ;r�eަ��/�\0��>�9�T8�5)��;`�_�\n��k�L�a(R���)���?*�vD}+� ���9��Ar�#*s�	'�*��{���-�\rc&�`�'���>fD ��'2��3\nN�\nV�@�ݓ@�\\�\r�3��\"�.�@�ލ�\"�P�\0�,�k#��3t�Ǎ!�/cO7��{R�8��8��GB�#d�Mn<bm�,,���M����)0��D�Kr\0�`ڠ���/��0�{L�+,b��0K,��h\\]�g�>�~6B-?\"�>q)?Ҙ�?p�%���J�F̟��g�>�7�6�α'C1 ��)B�\rC����]��`Ɓ�P\\�N�&����%�̃#�&��\r�Z�HR\n���Z�k&�;n~:l���C��Lu@�D�GJ(�\"\".J%d?��\"��\0B\n	��<��m �+��8����,��/�*8��`c�ABw�3D(���98[�,����\r��[��U�P�h<\0�\$��I\$�-�MG���F���@�p*@��\r:�/:�����d>ԗUU@'e�\r�&P���Uc0���'�@C(HsT>�h\"�*��W#�\"R<��lfk�Dt��f�MpI0�Z\$4�D�h1�p�آ2uH%.42l��\"�@���\r�	��ou8=���<��-(^�U\"`t �T�@P�]J#��C���	� �4�<�h�c�\r����.��zқ¬-t&��2I�� /��@";
            break;
        case 'lv':$f = "%���(�e4���S�sL��q���:�I�� :���S��Ha���a�@m0��f�l:Zi�Bf�3�AĀJ�2�W���Y�����C��f4����(�#�Y���9\"F3I�t9��GC�������F�\"��6��7C8��'a��b:ǥ%#)�����D�dH�o��bٸ�u�����N��2��1	i�@ ������S0���������M�ө�_n�i2�|�����9q#�{o�5�M����a���t��5_6̆Q3��2������b�)V��,��H���C��%À�9\r�RR\$�I��7�L�����su		j���Cj\$6�C���\"\nbf�*\r��4�����0mZ �	�d�\r#�֥ �����P�bc\\��7��(轶O��5Lh�׷�r.�7�\"L������L(�	²l:���&��� ��H̢H�`7Gb�)C�AЂ��L#�N�b��\\4C(��C@�:�t��\\4Zڒ�����p_	c���xD��ò89!�Z\"7��j@��� ���x�!�.=!��(�P��NP�+(#/p于����(��U�/\n�	���ݲ�%.Rr��K�!YP��a(�A����؂8:\r8����!��\n%�ʞ@������c]C,��0�7�C��̡��9)�\\x[��0�%NP�h�s��P��v�|)����C(�5��˻-!�P��3����A�	C��V*FLp@3�q�x90���(#�U�ȶa�J�~(����8M����v�n3��_��qG���xCW��4L����C�@I��4� �<ѐ�����#h�`�*e�睥����~�B ���Ϣ�К ��1��Z/As��*�G{C(�:W)��7E�/��cd��Q�݈?<�#3v���KHB�P�uE��\r�|9�r�qj\"f��.�j,�L1�&��E�%�>\0����%�\0�S 1BP\$�(��Z,�^\n�X0�8.��y�57�Ĝ�Ʈ1xT��-��8I���5��×�*ȁ&��̚�� DC�M!��\"�	��Na��)��K���3�t(�I��\r,������ʞ\n�Q*EL�R�U��X+%�C��W ����0��>?FUDE��IHS,��i\$8L���R��2I��\ryd&+�3#�K��Ou8����Il+N�\$ѐ��r�S�Q�UN�UZ�U��;�)!\$���|�4Ʌ���D���K�:���>%��\"�8M����|���\"I�qK&|�JF�T�e<@�2�g�s[���\n�۪\rLR�	J��a��Pqy��=��)�^c\\Q(aD�\r4v��\0P	@ě���a��\"f�KX �C,M��JQ���Z��ͧ�⠘�`�1Ĥ�²��_�d�����K�SD�9�G��K��0h@��8xkB�#~��xxjᕣa��0��1�ɽ�L�f��Y���żΈ�n#ꄄ��Bl�}P�ę��nm�EG���,1E��5�&5��bFJ)`*�C�WAH���.!�[��Mc���퇑�~��ge���9�\n<)�I�?e�q,����RX��̕oZ&@�jQzO�⸰�����0i�+!hJF�nJ�0s܊,F\"LM��XG�	#\$���\\�\"���1����A\0�h+~.-��L���*�ȉ�'\"\$��f5c�ԗ\"'\"][�m<Ii��\\~�\r`\n��\$����1��W&ݠ���Zٟ9\$���r�費(�&���TA�x�Ӑ�Ld���OA���\$�D%iB�<���x0��>�o�\\�;\nQA8	��)_%�/#~c�!\nĐ��\$Z��H�TҞҪ~�.9C3dN�!�����J���ZvI���v�P�b�[g���OQ�=��Lyp�e�/h8���D��(S��~�@�i���^:P���L��ƃIu�-�Ür�i�R�nZ���T*� �(cHa\rP�4�I��3�Mw,���;+6NCh�b��=�UƎ�2�-��FU��R\0fh�C41����6�O������T\n�!���D�%�E1�b��WE�k�7\r�\\Y�k�T�&*iO�4�������:��7����D�#��\"T�>s���P���Ѹ��{��Y�v��sS�W�;�`����b�2k�a���,�NJX���H��?����ߙ�7�yط`�:�^�\"�LL�GJC��/@�1-M���뗅u�84�����iu�:Qw����k>��<2�Q'^Cڑ�6G]AF�#W���[��E-t).w/ˋ�@?�Mg��Y�=a&h�j�����h����./\0� 0mk\rV�`�{O�����o�p��Q�Z%��`�׈�w�X4AB�\r�nPBۢ cR�jf�0kM�g�~=o�E��8�\"�.�.*g����g����R� ��di-��������M�&B]\nn��+	cs\$�lz>M��dx��k�'�?\rUp&ym\r��p���`�y02=��аJm�f������'=m�\r�������������n�\$!1(1,�n�a�ڴ���8l���\rO��ĬN6&r�lY�!�Q�V��G�6��z��q��pL% ��\$Ė\$X���#*^\"�VS�BC\"���&r%��g1�ee�\nB\\����m�~��艍�0Dr���� �nj�\$l�q^�IB�u͵��l��h ��tO��8�1\0��A!��E�\"N\\�\n��k#(h�#�(t@�Y&�N�ŭ�tRT�H~r#�O-�%� 06�`�PQ#B?\"�r��m'�q�s�4�iHtM')'nM'�Er�\rƏ)r�!2�'��� �ib\$B3D�H'�F�;��Β�H�(��-���RG.�I�E '�d%S0�#���ER\r��%�\r�R�r\\D(\0�2�R��1�)c�A��T �2��s72 ���4\$�}BH3.�N��/�&M��s�m6Sq#m�[r<Ksy/�8N/f<�n[\"�ڬ@�6�|�����'2�^��9ӭ-G�8	l\rf\r'R�C�P\n�ed�!/�ރ��0a�,��=�c>\"f�D��F,�K?&�>\"�#\rn�H,���N�0�D1��N�Aӯ>�3Ε��.B>q�d�Bf#td�\r�@b�\"gXJH>.��^#�J\n���Z���R<D.��#\\��AԀ�C�f�gH�yIIn�/�D���\$�A����&�%�S@:�	KMȲЈg�:���h�~s�T�s�E/��F��-�3��I��B@�txk��'!1��p8o��E�I��L�>���/(�t�%5(ײqJ�\\z��cT=�3Q�:�F:	���G�2'��\"�s�\r\">���3p����Ēa*ʳ�>�p4ƒ+� ��TM�@��n����1B�T��6��,D��l�R\$��s\\G>�\r[l�>�R�\r�]�I�q,Hff��,d�L��	�.";
            break;
        case 'ms':$f = "%���(�u0��	�� 3CM�9�*l�p��B\$ 6�Mg3I��mL&�8��i1a�#\\�@a2M�@�Js!FH��s;�MGS\$dX\nFC1��l7ADt�@p0���Q��s7�Va�T4�\"T�LS�5��k�������i9�k��-@e6���Q�@k2�(��)��6ɝ/��fB�k4���S%�A�4�Jr[g��NM�C	�œ��of���s6����!��e9NyCdy�`�#h(�<��H�>�T�k7������r��!&���.7�Np�|+�8z�c�����*v�<��v��hH��7�l��H����\"p��=�x�Íi�t�<(��íBS�V3���#����ÁBRd�+��3��*��B�ʝ�L�ޮc��\"!�P���	�؄;Q�j��i��ꉃzZ��T�3��{1/�c �Ժ���?ì&���\$�bn�>o��;�#��7��T����@X���9�0z\r��8a�^��(\\�Ncs�=�8^��%\"9�xD���k���#3ސ��Hx�!�2(\r+lL��#\n��&��5�C����tF�����'@P��0�\n�T� �(�C˞�ׅ��J�|����R\n%�L��!�`�F��P�d���t��6H���I\$�H�0��l�I|P���)�L��\rë��		Ĭ2���X�9�����K|�r�Z�����9<\rئ(��S~��I#p�/V�ka�\r����B�!���{��K�<Ǚ�I+:ϰY�9�P���{�\\�[�m�\"H����\"1������`��?|2�xܒ-,J5�ip��j���7Ih�3�mT��l���Z£�-Bkۨ��C���n��9H|�����&�A}����r��J�z�4Oa��Gx:�T���:Bˬ8�e�o�d��`Ӕ�9R}�7�k\r�B�ҡ.�TڝS�Q�P�ߒ�>\n�W��^I	\r%�[+�d�!Xk��,s�zYOJ5�Ǆ�+�g崌�t�Þ��Lo�-�d��.R�YL)�8���T��T?5X�r�si�@�n���!�\r��@�۔쇾�ʗ�	�6A��3sv}�\"AIl��C1�|����ưMڈ\r�L�ƥ|���!��\"S��ԉ���jA�Q@�Et\"g\"�������<\\�7��FEM�P	@��0@\n	�)���2��H��J)���d,n��_O�ז&�g��F����8���D�1�D4G3��ۧ,	�כ�+7a��@�ZI�C=Na�N�	)\"�a:A��FBdS\nAiK����\r�1�`�{�q�K�X��2���1�Τ�C2�\rw�I��#� �%̢��s�=!�̕�ɕR��}ު��M���0�� ����)\"�OO���i�l@Q��@'�0�%�:w'�æ�I�LT�1�:J���9�ڹC闂������\nG�M\"i��A#!��2�*J�Nm�y�FY)\$�WL�X&�3��ƾ��xA<'\0� A\n���P�B`E�l)(u�I�#�JH�ņ��q5Fkqd,��%2ޘ��B�#���HM��1�[�.&	{3�!%�v�2`P��[����n�j��-o��h�D϶��������ڏ�7��:thc����T��(V:�����`�ٖ��J�XO�S�MZ�b�@S�VC5��[@PC�H����%(iIX4���t*��ׁ�i�u���[O��&rdk��:��.��<߱\0I+�|��ɻ�(7�BR�u��?'��ęg�Z�tl�!��}�He�`(\$�ܟ��I �s�t�j�&ɽp�`���ق�������MK0��T��zsk�J�D:�\0��\"����2��FQ��[%x8���@R���P��b\0V���A�2^N�+\$D�I�]N~�(j��O�JX�)�[���W�\$#�>�wU��cn����7;\r���S1�����r[�up�x���Dk�/�tD���a�����&��\"g�	Md�n�@��t������dVj�9����|�����;�퉭� dB�\\�B���NCl�\\fC�]�i���5Dg���Ecn��� rTLn�u�;'��`K�y��F�~BH�O|�p�}���a�}�\nP�!�n�&	���C�j��vK�(���XSg���^�p�??y\$S1l�'e�\rb�U����;ڽ��];��TI{�0��+���G�>��~z-��s��l�7��^�y3t���oy���ވlֻoA�\r���\r���Z�w�=3�^U�f�[z�ߌR�����z5��>K�ȝ���Й�2&LʗN��-�PRf�����s8�s,�C��	{1�m:%,�K��}��-]����-�v�r�kN;��Y�p��0���.��\0�O����:ڙ�+�M��L��@(�9��#,�w�6:�-��Т'm�>�P�I:\rz�qb�F�:�.B\$l�jJ��<�,��M���E.,\",-�V� �5���	]��бDz�����0@�e����\r#�c�#\nP�\rn��,�Z��l\r�F;e�	�珿\n.�\nmt_��!Q���B�c�&)���@�#1�j�`ʞ�t��2�,�\"�|	�l�9�'FN���l���Nu���0��f��qbe�\n���hQ�/�\"�PZcXJ�D��l�v��A��fbh����.\0G��Ll@^��,����(����^���vC�Kc��M�&B��E�`�/ps��E`�`�-�8�\"�M�4J`U\"rC*�3��P�xhL�,�N\n���Z2�͸��\0n�xNkl7n�\"k�Ս�:��tͲ8I~K�E�_!�F�O�.cT�*�{bpK*0}J��\"p��jz��\$I�Z�b�M��g%����dư�.x���p�L��~��B'\\�R���r����6\$�g,DN6�R[�>�n�zczo����ᄻ0�\\�Rd%fl%��ȍ���S\0�;͸@����\0�HR�.�\rMb:��'C��!��3��&�&br�Gdl���O��#�(#�#g\\@�à�9�2���+Ǭ���7�꺠";
            break;
        case 'nl':$f = "%���(�n6���Sa��k��3���d����o0���p(�a<M�Sld�e��1�tF'����#y��Nb)̅%!M�уq��tB�����K%FC1��l7AEs->8 4Y�FSY��?,�pQ��i3�M�S`(�e��bF˔�I;ۍ`����0�߰���\n*͍�\nm�m0��K�`�-�Z�&�������.O8�Qh6�w5�����m�9[M��ֿ�5��!uYq���o�Ekq��ȕ5�����u4���.T�@f7�N�R\$�Y���8�C)�6�,ûBю���)ϛ\$�=�b�6�����h9�Øt�jB���ȣ^�K(��H�Ⱦ��X8- �21�b(ïC��,��7 �r��1k�N���,�+rt2�C2�4�e[��������Qk��c��2��P��8c����s_2����Ѥ�1?\0P��\r�bD�Hhԁ����=�j��Ԝ�?ʂ��:,3�ʀ��O\0@=Q�4�Np0��´D�0z4c�r�x�[��\r��Ar�3��^ٶ�,�J(}>ы��94\r ��|���F��ːƍD�[� ���o����+)û\\�X����!�x�x�\"r�:��J585���_�Ӱ��8B#�˂Xޏ̢��%m<���3��7�rY>��r�)����YZD	p�#?��d��L����K��0����p2\"̏�h�2�nLj5��\n3�S�m.f�#V1.H ��\nb��F� �9.�K\"7��t�N?���m�U��P��2;���\r{;�8�0�B��R2\"H�8hks\"\"���^mk��E�ͻ4\0Pיo�\n�6C���F��7Mz°�3ԍ�����b�9ce��3J�k��i.�>���j�ޯ��8w��2r�L���x�Q�c;~\"+���������\"���=��s���{�7��7�}���p_)z>F\\)�P�Șs:&�3(g�IB�h\"�A��`�0 *%E�%Pʢ8��U��Z�Պ�V��;��8��rX2BfJ����7FL��%\n��0!�6P�niX���#�]�8ظ�e�9�TI�: x��Y\$���2��`O�R�W\n��f�\n[p�8,���qL������v��:��{�:\0*\r4�>2�P�L��ِ��pHY�4�2/7��a� e5Rh�)��ꃡR%�yc'�C	K0s��\"17�ɬ��2��c�B�bM`D-m\$�d݌sIE�L'���H\n�9\$fD�1�O���\nI)�O���Bj���{A�;��^K*6H��gdz�)DX��rb&|�I��a��IB+�\"���l֓��@�Q�EL�PQF���k\"�z5�@�O׮�K� aL)b\\r�p �DQt�b�Ty+���8E\"JI�I+%�9D��pH�%='Ι�B���\r�:˹F��\n	8A�U��v�HbRI@�*�b�٩6A��*�6`Hٺ=В�\0� -SdV{ERvOjd�\reȽK��c��i\r\$b���k�z��E��T7��`d��>\r�&ٰ�H�\$�v���&����ǹ�V�uqM���b�K�5-��B	\rʡ	��*�\0�B�E�@�\"P�n�0bf�t&P����-�t�F�Q�ZD+14&�t�9a<6�M�t�M��_:O@\\�n��`2'�����	��~6�Iל��O%��F�Y����H�. ��4�d7 �4Ѷ3V#!������5�O�������H�/DVr�9<'�T��Vz_���(����%��)p�ޟ�2�Wik#�2�s��q�A\$����N	�nf���K��{^��y3;pl�e8Q��2�ڀ\nh�+��aw�-�h��3�\n�F��6CCq�\n��Ǧ�|�\n��Eԗ*?�/&���N]�\n�!��@C�W���υUX�\"���Q-p[��\na��l�0eXn�!�D(��뜪8���U�:���ڻ\"Tm��6��&��n�p[cfNG��]:e�v���Y�8�N����h�}������ۀi�f4�2!Ki�R\$�Y�IDH����Bdx�)ޖ���X��s���p��`�Ke\\�4�PQZ_#�{�@��K�\rO��\0���^k�R���M��H�c(Y� ���D�#�_�#' rJz��X�+�&Go�����֦�׳\n��[�����h2\n�2�^u��K#�y����aE.}˯���&�ef���/Do\"qeٜ����H�_̤e����Bro�)|�����&-!�7���07���{7���p�s�w����#A_�����?�aӞ�,0�5>��;l�n�z����z��������+bT��p���8�?O�ߞs��A_�<\\?��,�*�ZF\n>�\0ֻ`�cg%���`���\r�|�#��S�\r�PQ�,*&/O:�Ȩm�!p-k�809��!��J���N9������,�!�!��-��b��:�\"]����Ɯ%��L�����\n�O�*��T���	���(�H��J#�#�\0�?��·''7\n�P�o\0m����Jid���8F&���\r&��H�x�ϊ����~��O�G����zf�#\rcj�O��� ^�9fh\rʱ\r�{15b\n�E�iĔL�Z;�^\$�\\�1`��\$L���G��4�iqm�~;j\$qz�#b��q�.���<1�L�qb �N�2f���\$����6�l�'|rN1���&��Ђ4��\\\r��\n���h!%M�q��v�Q�8�,α����z\n�TIP��\\�����\"�\0<�!�?\nb�B�F��}��1��\$n�̅�-�za�z\"��}`��.&�&����:\r(-͜�N~�f����i2�_��	M�ಘ\r�� \0�\\��k=&ȡ�j��5	x���1C.	��A��*\0�\n���q\0�@8�\r	>l�v&C�\$��!��#a^��.�n	/��/�0S��� ���ke�(�[���L�qBd��i�8A�� S>\r`DQ��5R�K7c	��eV/BdyL�sn�J�G\"�Dc�;f&.L\0002�&�|�.���'L&���9��#Ӏ���:C��ӝ\0��)П9s���s�;Bk;�{&�Th�0��:s�]�\ngrR��>�Όd��KƤ��̐Bjf�?��2�⣶4%�L��=�6҄c#6'��(@�M��LI�R��0|�ð�S�&i�`#\\�(�D�D�v�k�;��\r�J4C*G�u����L.Cz��##�B�\$`";
            break;
        case 'no':$f = "%���(�u7��I��6NgHY��p�&�p(�a5��&ө�@t��N�H�n&�\\�FSa�e9�2t�2��Y	�'8�C!�X�0��cA��n8����!�	\r���࣡��\n7��&sI��lM�z��b�'ґ��k��fY\\2q��NF%�D�L7;��g+��0�Y��'���q�H�������16:]�4�0�g���ۈ��Hr:M��q��t������醡B�����傽J�G���\n!����n7��S���:D0�LQ(Y��e��9�3�^����;�#\":+(#pص�a\0��\rmH@0��j��&���i�#M|:	�(���(@�\$�H�����-�L܉� �;'��2��\"��B	��<��<��;9G����p�7B�����7Nc|���p�!Cs�69�h ��jڤ���@� �����c��\$K�&��Ϛl�H�4\r㫞0����`@RBf3�Е��t��L1��9�H��!}%JC ^'a�ڴ�kh̴�I@�7�x�@�|2K�P�� ���:����5���2��հ�6Cb�'.+\n��7-�:\n��p�7�� �X�65�P�\$�2��WS ��z�5�x �ǌ�0�:��x췎�6�P��\$#U�%#n	��b��&��;-,���&�x���5R0Xc&���\"'��h\$2c\$����\\Ah�1�l�&�^���SN8!@R\"�[�[�B��̂\rcP��Bz.˿{n�0˯��9�ۀ��[K����#l��0\"*C�����i��!g�v�g��\"���P�V��<������<\"�rEa�bb��:��U��jP6�B`<�\"�	�c0ͥ����Q���7�Eʄ?��\0003b�ضuT�cr��\n��'W�UG�@�������\r�yq*X莊�F~�����@�ӅTN�cu�����@����S��P�5J��JsU��X�^}�r��A��V*�#�L����a0<�d��bkLGAH�@ܼߙ\"N��4\nP���O�K��7��T��;����hrU��Ӻ�W�˅��B��',�4߹��i�@g\rD���\0�̙A�0�5 �L	Y\r�;#cX�d^!�ӿC4�`҈%(8�����Q:�!��'���ޠlzɴ�(�I�8sd�,1�h�Kz#.EС(�cI��e�\n�;��y��@\$\0[+�\$�C`������X�n1(,�І��Pc%\n	���C)t�g�ѭ�[�`w/Ϥ3���_�d}��@�\$ȃt�)0ѩ\0�S��Qj�7�PV� 4��i�9��PJF&M�\$��!�0���og`��3U\r�&�F�i�)\$\$�Lش��MJ(A�(��I!k���5���:Hyw��x��4�TZ�&aŌ���{I3��T4�4ء�I�@35�R&iVa�`�^��Dap /�t�ĽX�r�6d枒�bL�55���24֬�u����i�Zy^\rfM�C�*EЕ_'!M���5&�Ґ�P(\"����m.�>{��I�3\r�!���Pg�Ajea<'\0� A\n���P�B`E�l	~&�d�WT�v	Y,Yqoc�9�̄�~�\r���{�wR/͹u���9g9kP��7����[�'!��P�vH�9ck-�f��܅a�����l��Y�\n˪T_D�lV��4t�x�=fA���X(rv��L��al>�Q<��Ў\r�]��p��C����]8��!��מ�.�����v��ⶖ��[kt*cScI�[�,V�F�0M4�����o���E����\\�d�2f|e&r�ũ㔃u��1-h�3\$���Ah��1S�Wt6�\nJ:	�d�/q�7�=*{tC	\0����=MeFOL�1��F�a�?��bཁyN^�,2/U�K�\0�Gh�s�xKR���W���{�;	�l]�6N���0��AK���?1%	�2f�RN&�&/{X��611�,k�A����8czh�LM�#QG��R~��\"xσ�4�AJ!� �C�SiR��\n�1Vԣe��	{���ta��q~#:��9���Yh���L	5!n�*��s8Rۇ���tub:������b�c!I�F2?Lj]z���U�!y�P[49�Ϣ]�{5��)]��ҌJ��D�����Z8x�x���(}�����;ZV�l�,�g:�߇>;0�s�0N���+䃯���P��(b���0�0����#�z�9�M��zS��{�#�M��3��z<9�����,�=߽��'�؍�\\4���;xl}���������w�t��\0���ųXd͹���o�ǿO�!�{�y\"}�Yo��~���Ж�j�ǚ�j�����cP~�d�N�c�`,��5�F�n(B�˪�gpkP\r�,E��d8�\")f4l��(��&o�m�V�\"h#��>G\$̦����<�4�o�t��E�b�dO���h�#vN����.�������o��0�n����B��������6#�;a��J%��I2\r��/.:�M)����/�)���1�P���l�Xz���e��=�l�x`'n�2d K2r�����x0��H�0B�vD5�0`���Kd���m\"� �>��W��`\\���I.����qQ\$�,� ��h\"I.�V�I�ά��F/	hb��ձ�����.�J��ՠ�Br���m)��σ���\n��ѓ\r�ϣ�� �f\n�\\h��C&Jb�	1�\"Q\n�D�Ӱ�Ɛc4a��` �\r%\"p�pc�B'n>f�^�B�����\r�&(P�B�j���_�^�ʥ�2h\r��(-��-�\r-�&�ۯ�)��Τb\r�V\rd!�6.��\$��8�����c�?h\\���\n���pvɲ<�ޢb:م�)\"Fq����Ș��(r�P0)nRc��뢃QJ�OC���2B39\0Zh��,#Q,c\\5�R�jN6�D��6#����^u�	h!pRb� 0�i16���QEƴq�6��]OA72�o��e��l�-�\"�7��&�3�B�8#\n\"�P2L��f�p\n�\n\njz�Mc�:��\r��:�92�dCC=��[p獸:��&h�:nS�-�IF�!��-˶���@#�M�t?�0��2p��OMA�_@���J �-K��z02��\"\$�8�؏\$ZE�\r ";
            break;
        case 'pl':$f = "%���(�g9MƓ(��l4�΢劂7�!fSi����̢�Q4�k9�M�a�� ;�\r���m���D\"B�dJs!I\n��0@i9�#f�(@\nFC1��l7AD3��5/8N��x�p:�L���� =M0�Q\nk�m��!�y:M@�!��a�ݤ���hr20�gy&*�u8Blp�*@d��o�3Q�xe5^of�!h�p�[�73q�����i���y7pB\r�H�L���>\r���y\r�+ry;¡�������\\�b��@�t0�.��\"�D)�*a=K��S�����拎�;��A*�7�N@@�n)� �2�����M�����t'��5B�:����p�6�n3޵��藴�򂊌r�7�K�җP�)���#��|h:K�*#��\n0	�65� P��?-H��6F�N�?.Ȉ[�\$AH�޺���\rP�7��H�4��¹9�2�cU\n ���69?���;��+C��M�����pA�cX�x�ƌ��D4���9�Ax^;ׁp�H��\\7�C8^���P�;؃ ^'��b�mB�7�\"V�\r!�^0���0#��)ʃ����-Pʈ6�J�,#���n�\n-^L��6P�t��1�x�	�LH���\\���\0�:8�\$�⸌ω�C�%��df�J��?)a���#�z�����̫�z��#�zb���@6���[0��B0�7\rm\"� ��çIc���C8�=!��ؘ��]���iX��>��&\r+���@��y�����rɹ��2V3�)\rètH9�#��_�K��;Hh�(��P�:�V��/]��3�8�	?s��� ��9;��4��}Va%�Q�@���0�̌,���sx�/NX�\$��.t�B*Q�l�o2Y�W�\n!�������9�>����[Jޟ�Ę>d,|�\r���|g��E�I�)-�ؕ\n�n��K`�D�\$|i�@�ErMq�B�d��WJ�ɒ}p1\"�TA ȳG\r���6\\	{RL���C\$?�YQ��C�aʭ{�(5	|&(��1Vo�t0�PV�h}�L9qe�Cx��lD�I�@��Yh���I#\\9H�4P�P�S!�AI\"8>���U��X+%h��ºW�Q_�P��*�\r�����b\n�Ɓ�q2��\$MJ4��p��J�?00�4%	S�gg}���N�P` �]��B3'�A��WG�f�U��Wj�B��2��Ẇ�\$V�sƹ���8�L�ϱ0�}S0�H������K�P����ȹM|m���RT�n\$�9��JL\0P	@�Á+P�zB���{=HtbJM���Q�f�m7�0�Dى�j>;�IFq�.���?G~�F(�W�l�R�D���\nL���B\\�s���~Ce0����pD�@\n	�)R��:)��t�H\n��Tu>���dų��uIj��4�f'��{C+�&!�;%�CiAs�R�2��D��`a����gmI�\rL5A���}=�4��z6���\$�¹��F\$�<\\l40��0.N+�<�P��A�:��r[�C�A��5��}H�+%��M�\n�CJ>R���@���%��@D��Y��x�Fꠒ�A+ž:Lh>~Chk!f���k��ڌ�E��Z�H����~�u>Tp�gr��?���p^M�p��P1	a�5\\g��B\"'\$D�90�8H����܄.K�\"kDtE���)��b'�j�8tCi\$	l��O��Y�\$�;Fw��1]\n���4�A�JYɄL���G�N?d5�Fm��l,�C�U��&�8'��aL9�%�p�`��������(\\������fDY�s��D!�d���ˀ����O�P��E�FVQ3���S*TMzN�ۺHj��;p��j�7���j�Gf��tG'�'}1���O��ROh`���tAB}tm���dͬrȡ��B���Zc�ɴ'�\" ��Q�jt)%�󢫉���ԯ;��\rk0DëP��W��7�d\ro��W�����Ȑ���_���:�.b}�N�\rw�u�ay�\rx�&���l����{xX�|y��Tn1�I�y囍+�(� ��<�H�[]f�VӬ����P�W�0fim4�c�q�!H�����	�r0 �@�BH ����u�zz��}�G=�ηG�S�=�0U��La�Y��wc!��o����H�Eܢ�rB�w^�d\"O����D�Dc#B�8(���0y�)k��?\nC|<e�f����#ߴ!��A�O�S�^���^S�yo������;��oM�����{���5�</Ʌ,���`�cn�,#�XNCȈu �#!��uXkL�������Sͣ��(��O����r��)S_�\"C�J��%k\\�,��vC�(b�%��x��h>G��l	�'��L'Ƴ�y��l�`7@�) �;nx`��f�D&;�Q/DLO�pX\"�P]`�%�����\$�RY�w�\\4J\$��/b^����,�,A�	PV��b��<%А�p��^eŘ=O,�l�%�v5`q	t4�gt&�<9ϊܰ`>�.'��\nrf*�qG\"߰�8Κ�,��fuG\n��	.��	���h�)���᣼K>�P?��D���	�[�`��w���I1N�[M&OC���Bg0X��\nQD��+0��\ngj��M�Pg��vѓ���ēQ�qq��/�\r�߫|��0��O�.O,�O:���(cqr�ѺJQ�Qg�j�@�e�0�\n����%��1���&�N8����^�Llv�n:L']Ck!�-!�5P�ܐf� 2N\$˃L4�O�����hB+���pn�\n�dhp�~-Vh�b#`�EI�pᄄ��X]�Z�`��?	�-'F��2#d.?bVFa�&¢�\"����m	%�?N�%β_���)�����r	���h���.l�#QQ\"QV߲�_,��E�h\n�\\�0m�P�����p�#1��h�2J�\$=/\r93HO3�J��L�3@;szg���R-m�G��n��d6c�7��mM�ˤ8s\\��^�,���FRLn�Q�R�3/\n\$�/�e0�9s�0ѧ4��4�@�F���,_�3:N�P�Vԓ���1g�<��<� �\$�rzIc\$@�4���R#\nM@�?S�:���5?2����#��-�S@t=�Xv?ld�13���K2C@�;��=3>�l��	�RQ0�E.EEr�p��U@�w;��uN�S �C��e\"����g�C�9nWH-�D4s\$�v^ncI�D�[HX悸AN�8B6M�*xiR/9H�f\n��x�#\$4�\$��Lt�L�!�~9EQ�6����wc5(D����3d����!O��f1��~nmQ��&g�#QoR�\nxf:�0'G�b��;��\$&+\\����a!+�_/�h�V�c�>+d�\r�V�b\"�baf���*C	�8�z;\"n�C��QTR�Pb(B�p-\$v&1*���\n���p\$�@Y��,k\r���2\n��\\ǒ�������24C��z\n�?�6(�hN���!2���k��q�M�Z鵔y���i�&8��X�'f�BC�\r���K0QB`���@�d/�;e�A�I��<��~�F+�L;r�l�4+�ބ)X�,�h���{�6��f��]�&�`�V~/v�T;3��s`�7����v�9��^�����7F�����jT���\n`�\ru�LG�.Fs���q2HL�d*h^@�v\"�m��%�`��lh,�k+�ާ���4��te����J�ax��U\n���@Z�6L�B�U#fxd�`y�\\�;?%C�N�f�J\r�w+G�;b6";
            break;
        case 'pt':$f = "%���(��Q��5H��o9��jӱ�� 2��Ɠ	�A\n3Lf�)��o��i��h�Xj���\n2H\$RI4* ��R�4�K'��,��t2�D\0���d3\rF�q��Te6�\"��P=Gࣱ��i7���#I��n0� Ը�:�a:LQc	�RM7�\r2tI7��k�&�i��#-ڟ��M�Q���Hٳ:e9������a�l���])#�c�s�+�Î�,��q��X̸�����q9W|��=�:I�E==��\n\"�&�|q'o����<qT�k7�����N9%\"#p�0�(@��\rH�6�z·0���H���3O��@:��;\n����Z�*\n��'�\0ԙ���R��Cj̈P�&��cȒ�����錮0���\n8�\r({c!�#pҜ�#�,�9�RҜ��Cf�Ha\0���3o.<k272 ҄��#Lƹ�)|�6M3|p\"���ʰ.sӒ��S� ��j�@�|��c���cƲ�/2�0#�;�#�`:Ӱ�#	�C X�����D4���9�Ax^;؁t�I\r�0\\���z2��`2��&��@��˚Z�9��^0��H����Mp�)�21xՃ�|1S�j||�i��<7��'\r�%�͘+����3��(�Cʠ���4�Hx�:�����l��1�p��e-�@ Տ�f�k��2�C�����s��S���#���ӥ�(���w�8�Cf�J\r�{�	��2�Hy{`���h���	RX�&�d������ZH؋ԭrƍ�B\\����&L����_h��P7r�`H�a��\"過|�~\"�#����<��7r�V�4��b�	#l���(�۱��8�T�\$r!=/N3�lp\"3\r������1oބ1�n���u��V� ����4��7��3�d�\"��d9,�\"늃zb�0�\":����� k��ȫ3��Cg%�m��:�`e��g�_\n�r~��4��(%F����G��R �+6rdRڔV��\\+�x���X��c)&�b�\r�������>����.%����)���\$ ¬�rCoH�R9��*�	!B��V����z��\n�X����V{ن1j�wj\rs1����l��I�~�*���q�� ��D��m\"���(��[^�T\$��1�p@��@�x9Btl��f�p~@/\0�b�UF�DlY#L�#���s,�z\"M�'�ЈB#�M0؅\0�c�6��R�â01Ȧ��*	HC9�&R�&dj݀i5����8lM�,M��2R{��n�(��bfz���\$�h�(ŢY`l����VFE�)��#\nH3��UD�j*\$�7���R�6�\nJ�#g\$�-�|GK�_%�J���sR�B@�ȚgP��k�\">\r*��R�ie1Ip.G��\n:(��HY�@�ɣ@�mB2��Q�3�l���NXa\\?���1��=ZR�	�a@'�0��*9-{k�t|�UIa�:��>oIi/!U�0��Ҩ�Q�����B�Q���)�B\\��\0oHJ�1W\n����@��`�6۩H>Gj�Y��@	\0�Sb�,<�����s\0PO	��*�\0�B�E�7L\"P�n�u��r�D~�/\nta�)x�]�&D85�b�2�!��a�2jM��	R�EJ��n���4����.\nuI�AO�5A�	)uI]�%�0z�م��u��&�S���������I�k��jpLȩ�x�����Y�Y\n( �B�#�i�bW:A���5�+�.���|�PTxtk*(�Π�hn0y|��4��gS��)Ţ�\0��Y7�.��R�0Nc�y��������e�ⒼƏ�����9�G���TD�O@ڎX!w.:8�4��rB�@� �\\��WHk �x2���Ik%ZVz\"��+�օ��I�s`w�G���v��qjx��3ENk�u��'��qn�T!\$�]K欪h8;y�z�\n/uM3�~�AySc�\$�68�����g3v����UdVV{���R,R���&u?��_x<��|p]�X�7	��g\":��Q�|D���;��\$_�^\$r��	f%G{��|`d�+��+s�L3%.�V֤�Y'�?�Òc	��饟0c,�z�G�i�Y��HQ�Ni�>�ܻO#��2�,��9�XE����#4ca\nw,ǉxdr=y��\$�\$o[�]�Rv�8\"��:��fDX�����^U1,��v����2~_CL?�b��0^������._�s�އXot8\n&���MZL�0m�p�!K_ug�{_��γQ�^���l�L��:I��8�jU'H߄��[�q��K\",=�1��T͑���c�ż����N�o4��:.�x�B�����t�Be�h�Pt���.�&�˭���F�n�n*�.ǂP�2��Z�0:��yCc/���N�e�Ԣ�:LA0	PԆ'�&_n��wpz��f�	*9fF/8z\"�##�냜?)43C�nDlbn6O�Vd�b�\$�>,/�т0P�Dk�	�(�0�|�V����#8e�\n�b9ˇPd���^�b����L\0M:��\rd~\n��,o�s������1'��ڀ@_�=\r��pn��!�b�p�i\$!Q0��8#����s�] \"f�o\"lH�uϲNEC���ƨ:bm1)(D�j����_q�ٱ�hE�jiN��qLs+�ّ�a�4��qbw���d��o|My/��\$�k�P��P�����]B�ps����0��������� \n\n��1D�\"�Y!c�9�OR@.-P�#JDc\n\0��ļB���2N62R?��-�\$��&)U\n���\\&~R���-f.�k#(��(�'��b=��<\n��Q�h�*�ܩQ=#����sQC) �,D�!�-1*@	\n\r�\r�G��Mx��8�:�H@!O�N/��\"*�01D{��k��F;\n�f0\"��c�R3P\\�k2�\"�44�< �j�\r&�`�D� �[Bh\$0��������Ef�\r�:Ac� �\n���q�r1F&��?Bl�PF��X�S����'�{iά�I+EG1\n1`��,���)�,)���}rs<��6I��.J{�Tb�u�:	��<�\\&h�tg�l��Rp��F#b`�\\�@1����64C\"�%�4NlD�C\\6-E'j�\r���x,�B�|��ʛoTz�C�f#��9�%5R]B�g��s`�y�����A����,(u���D���x��:�m`�3�0��\n�ƪD�F����&r%/�1ė6M\nFI<��#��������P0�N@�.�� �\r\"����\0004�.M�hB�";
            break;
        case 'pt-br':$f = "%���(��Q��5H��o9��jӱ�� 2��Ɠ	�A\n��N����\\\n*M�q�ma�O�l(� 9H������m4�\r3x\\4Js!I�3��@n�B��3���'���h5\r��QX�ca��ch��>��#�Q��z4��F��i7M�j�b�l��LQc	�NE2Pc��I��>4����1��e�������!',�΢A�+O�_cf���k�NC\rZ�h�bL[I9Ov�q���Ÿ�n�����D�,���\\�(���ǵGM7k]�3���c/_4Iț�`���&U7���094ÒN\"7�S���`: ��9A��9���ȓ��@35���˄�V7����2�k(�R���Rbγ�:]\r�� �@�j\\9.��� �\0���Ф2��(#���ھ�\"�҇��h��(h��7#��\$/.�<�H\"�|�����1�2K����P�Đ@��+� 3B`޿���~�#*� ��\0ܟD�|������Ʒ�/@@0Đ(�<�\n\\:�(t'\n�C X�\0����D4���9�Ax^;�t�G'�r�3���_	\"5`��J8|��N��3/�Z�燁x�8�`Ƽ�(�y>Bj �:���T�6*�\0@��HM�zݭ-�y	�z�%��\"�+��t�� @1*�����lcx�:�)D��c1ˡ2/L���dC� ��b�k�������t��\\n{&#c�Ob�`ӡh�(��w�&�0I 6,�'ը�'&�����I�bh���JV����<eT^�}hb.�n��1�����!B��&LS�>�[& މ�;�mCtq�<�jO��*rc�+ ���s��d=]ZH4��b��#m0Ɉ��^�r��=�1�P�s��	q\\����1M�@��������5�D���r1�VĪ��U��5-Z:��0�60\n��̥z^�1,:�m��3f��Bu{���#8�`��a�9TPP�I�\$L�0�@�Ky�7��*��m��C�yF,@�ى�(jAX��h��ºW��`u��_*X�\$7�n`V�5@���зV��\"&��2��\n�A�m����Aj ?eٔ�`�cW��Y�X�DcD�Ղ����\\��z��\nÆ+9,���a�ZIևh�b\0>y���B\\ÙT|!�����y�8���@e�+K!�׮�kC�2D�\n����� T��Hx2�Xpa�e1?\"����\n�?��J��]I�@�ǪA��hFq�����D�)\$�H\n5�̰PQ�I*��ȇ2T�\$�d7(��tC9�!���V�\r��R�}�ĘIς�\r)m�6�`�1|�L�I�pdz-�?HP�DZB�p;�\0�#�~W�����0T\r��0��2֠\"���`@����!R���CaK�\n'N^.s�L	�4�R�%�P\n|0�E.�順��%#�~*���@�ɪD�\r�B3 �4MH���L[`�1���1�DA�\n�(�	�O\$�駲�\0nI��\$��K�1-������ʟ�f��d�s��~i���\0��G��j���I��߂b��܎\0�&��;κF���=^DU1�ZEQ1�%��#�Vbܡ��	�8P�T�^�@�-� �IP��6�X\n9��@��1EO+f%�����\$�Y�g��u�1fLɪ�`JAMT+�o��\r!�%�f��\\�z�P\"�JP �.;���T�r���<=��\";v�-<aJ�A^�zq���?\$v��G\$T�;ǰvB�>_\$(����oR)ePLC4f�Gz���k��PA�M�'hƠ[C_D�ņǲ�HzĬ�(�vg:LQxh�;�>�0m,K��+�Y{�Re��ܲ��JS�b6��8��Q\"zC���hR�0���㡰� v�����\"��y�5�W.M칓��5����r ���D��	�\n\n�����`5iƁ�#-�z��y�{!��v�F##ɽ�(��C	\0������S�� `l�b^����\r4�M�o�i�1��U��Q~�4{ܐc�_�p	���\"���u��޼ҥ�U���6����r������t��HC� q\"����@�P��'�o�juͻ��|;�ăo�,��l?�o���a��:�ӨHJk%��C,�W�	ܓ>�e�r�#�\n�!G�6���qD�t�p��k�觸�tIu�]�pƶ��yWU�#��)6���Bg�����bi��֞�'�^(���O&ӈf�巕3�F�*f��'yc�1'X ˆ3��,��\r>�@��k=���uPz�ܕ(%�OG	ܓVTf��T4F�	@��OQ�4�^D!��A�����/���(S���\"���y��z1A�U�Wc�>���Iî����Ob�Kό,4�P¯~4C�G,7\0�J�,,�N#����H\n��4��p��Ds�H��2��Rso~0O���6%C&\nM�0�n\"�@@��*S��\r��N{��#n��:z�w�^���0J��'\n-8���\0ʈ�bq0@�c\nM;�������4c�������tb��d�\"��<\"��l&��c�^I�*0�2D\"���;�Z����d�#�9�0V�|#%�ɱ\r�����?\n�\0���C��C\"���M3F:�`���90�`A�w0\r�{Eڜg0��v��Q1��0��D٩Pf\$���O<]Q^�é��c�F�2�&g��hc�Qb������d�p�-�1��0�P��1����PZ�Qv#��*�Ғ�b�I�i��v�6h2\"��Œ\"2&R/D���.Ϥ��(/�Z�d�\$B�\r�?�RVLaU%�Z��&@�%�k%��0�!�/'�lH-��҉�?\$1�'�Odc���*^!�4v�>��H5��1b2D��'�/+�Y+�o,�Q;,�G,�[- �,&��2�*���mZ���c&�e/�!'�A0R�/��'��Ҫ:��4֍32-b%A+�/O*ڄ�/3˚LJm0�E3ē*o`	\r\0 ��9�\"�(�\"C4b�8S����Q�ޒ!7P/��7�t�@�j��a��T:���&b��-�s:���\$�-�����<��j�\r&� ��E�!RHCN@ȣ� Zj�O��\r�>A�� �\n���p#\\\r�\"�J�oB����984�������!�\"\$�\".2�:�+\$�l���3^1�� �\$=��(L�|\r~?#�2O��H�@��27�ڷ����OC��d�=\rt&%H!�E�&�\nႉO8҃oDP����]�\\�J�{J�-�jM����AP�-C����\r�1�-;�0|�|D��n�oM2�?���(�,��Dц��.D>o���u�O���LORK���-�lBF�!F��ED����DF�O�L�1&0'�>�4Is�3R�c���!=�]�%�+W�L���/��'ri(�f�kH/vG�8�rH'�8�";
            break;
        case 'ro':$f = "%���(�uM����0���r1�DcK!2i2����a�	!;HE��4v?!��\r���a2M'1\0�@%9��d��t�ˤ!�e���ұ`(`1ƃQ��p9Φ㡕��4�\r&s��Q��Fsy�o9Z�&�\rن�7F�h�&2l��A��H:LFSa�VE2l�H�(�n9�L������f;̄�+,�����o�^NƜ��� :n�N,�h��2YYYN�)�Xy�3�XA����K�׬e��NZ>����A���#\r�����y۳q��LYN[�Q2l��Bz2�B��5��x���#�𕈌S\$0�!\0�7��J������;�\"V#.�x掭��/qp�6�������JҠD�R`�*	���0�P��.B,�Դ����?JD���229#�\n�H��/q�),���#��x�2��h2���J�`�¸+��#�j\$-4�.ύ���/\0P���!0�3�@���Ή�x��ÂB���*�Έ)������y\r �<9)\n�9�o�BL*;�CCe\\��H9�`@#CC�3��:����x�o���uT�Ar�3��P_זX��J\0|6�	2�3/	��퇁x�9���1:�d�#�*:E1-���7�(�*��c��=UOɇ�#N&�2cbF/�X���K�Ҋ�ܽ��(J2~:9�k�,�H!�#�������H럼h�ƍ(t��3� �3j#b�;/��)�X#�#,c{�#;�Q�y�,�T����?���[��ƴ������3����c�ѳ-	�\r�#\"1G-^���\"`@8�Ð�o#���n���\r8�����c�uV�P������	�I�x�u�*�=���t��&|\$����ǝ�`�O&�J|4v�SB��OD���i2����Wʟ�����V�i[������6(EH�C0fOʤ����G�hT\r��T�\\h�cWa�3ب�z�N�80�4b�Y��!��0RP�� \$`�3O�1+\n�����S����V�2B���=0�1h��֪�[+mn���Ur�\\��t��^�������E�����IX��VX��*�-�����s:���DdaI�0,����(~�J�D��64L�֊Ӊ+am-ż��\\��*��⪟�[^O08T�Y#>}����k^ȁS�&2�c;/G)�2�'I���#���2����	LA����L�\$0�9�@����\n�\n�H.�\n�8�`�Uk\r%���dt���\$���k8��:̼1��p��� V�9��PP!;F\rʦa��nCI�@l��Ò���&T�S�Q��:R�Ppl#S��6�f��w{���4�	E�F�`�CQʐa��:\"dZּɛ�y��VRҞU���L�\r��&��M��&��-EbDI	�5!i�2cZ���e�H���Q�JU4R?��aJ@3�<�\$,Y��4!��s�x���#h8���%P�c��V��Bo��F!���\0�¤�1lY�3�U�yp	���2�CY�X.�*��-z7!Iү��Gb`g�\n��9�JQ�����\"�:�¿�0�)����M��P(�	\n�j�E 3X�N�J�g�p�π�@QڤL���E,��c�}_��~��mj�8�|�UK>I�	d�	IW�S�>�\0�ƕ|kQ�Z��0Is��Ԃ�=��� ���`���4f,����)N0��N�[�CN\$5\0�3�]r�z��#��xYO�F#LY�c(�B�I2O ����p�\0>�]:g�\n\"�\nK�u1W32����Pu0F�U�_2�}`I�`J\r�4g��;�,�����B��:���\\fT�y/���vC�k���F�4v3�<�|�X����CW�.]�o}�:�bip�u�g��O����(C{��5�Wo����\riA�� �G��q�;��πA\0(0�I�m�\nPdogYS#9\\��*Q1ړ��Q��PA�2j�F�+g �?%RR���P�Y-�Ѥ�G�A\n�P �0���� �Բ�+d?E\0PGi�dц�9�lK�L��V�++ze��Nk�PQs�8�06�s:�<ޖ_�|Ft�	�D���|dz?浘���zĔ�V�������7=��@���S�zy��ӌt�[��?Kн]�~���X)8;�VJ���;	\r�&F/�I�h��RJf~���B*�ޒX����T�UD��u�/m���L�) ��{��i��Q���fQ�r�8��m\"ŜΙ����d��#&\nY[�VA&l9y/\n�#\n�\"�VTJ+�o'��g��\nA�\$3�j�c�/�C���󑿒L���I��O�8?���i�=vDdF|J�v�(.�\"���j�L�.L��l,�\r������g��g�&��ximVւ�G&�����ΰ���L�b/mXq&|dL&Ne,L��dE.����L�O���`SKg��	UO���r��6��z���	P��0��P�P��'�&e��F�Gk����w�����\nR%L2��0��.�ݍ�<�.��CE\r\"�\n��� P��^ʍ�.�5.�5�}	�\0�+�xJF���:��-������#	Q:�q>��\ng���b��l-)r!*������	�ʃC�)p;�1g^Q��5���F:ð��fՂJ��\nU*D���18���͐��B���m`�Ͷ��3��Ԇ���E\ro��Q����	�4���b��p��\n18w�b�����Tp������o�z!��!\0�2��2��pm�G\"�x�c�\"�2z��GRD�O�b, �aDHX��n#�<�Jߠ��t���L�ߤ�1�2}2���2��	�̥1��-\rp�'�ᒤ\"��+C��h�X\$��!�@/&|AV4 ����!�r�!���2�-��r�.Q�o�R�`r�/qSr�*��02�UOxr�+�A1R�\"�L��.c������6�ꏳ8�26�}����(�%TB�T�MsR6�x2sZ/��63P\rsU6��5����hل���#*��O��S�Hs\"��-s�\"P��A\$�9��3gr���Q\$�y� �@��Q�9P}!��=.!=��)2==�6R{/��	({�)r�3��T�3��\$.�(�	��\\l�gR�~����A��1t!�h.U#�d�N2\"Hfd�@����0���Nl��-Br'>��n�5�~N��k\$c�x�cp�ˠ��L,�DW��G ڇ\"�2jf\0�\n���pGB(ף��hJ�f��W�픸\"r�=��LT �!jp\"���Ǩ�d�v'>M��G�<4n0	H)xǢ9>n���γf���l8\"�\0\"q2�v~ˠf�襃���*f\$���BFJ�fN���`�UB1FF�C�(�\\Ow/PUU��_���un��r���Ul2�q���-*'�h6�l0-uTM965�\$џ UNc+ng�aN�+mvf�J��p�w[��\\���2#��D04�\n�Q&K��(\\��	��ZF0:&�H��π�/�ϑ�F�:ots����7Vɕ�	�&�-t�g�\r������R��'��!O�4M\$�c\$`�	\0t	��@�\n`";
            break;
        case 'ru':$f = "%���)��h-D\rAh�X4m�E�Fx�Af�@C#m�E��#���i{��a2��f�A����ZH�^GWq�����h.ah��h�h�)-�I��hyL�%0q �)̅9h(��H�R��D��L��D���)������C��f4����%G��f�\nb֬����{�R\r%��m��5!s��,kP�tv_�h�n��]�#���Pօ'[��\$����!&�c��h��K'FA�IE\$�e�6�jl��l�Ѭ�2\"��\\횩m�K�V�7�ťs6����P���h��NC�h@���zP�<�������l�:\n�,��c��;�j�A0����p�9m��#)��Đ��~ZĎc(��1^���Ӕ�0�7Ϛ8�Ū��G�H���E� �*��8�C��`�*�c�	���.��.���8��0�	��9�\"\\�ҫZ��H��8M���\"�?>jRʴ��vȚ��k���K�L���d� ģ��EQc*�\$|z��2�qR��*JC���<h����|�5�����J~͑o\"ء�(��S�ς�Z9Ԫ�#A	��� ��Y*W�z�i8���(vI>��6�\r.����ר��O�OJ/=N�9w#П4�# �4��(����B5'�k�֢��D_�E�R���s/C\rS,[�Ӆ�G��\0x0�@�2���D4���9�Ax^;�pÇb�\\7�C8^2��x�0�c���xD���M�F*+�~�%H����2~\\�x�-|�%H&����R��ũD��	p�v,Q׊z��ֳ��9�@����7&��I����K�.��Qi��P�/U�(ɑ�	0�v\nK�����b�=�՟�-O�zhKb��?I���x��N�p�&RӋ3yRR�Fr��h�q̷\$J+�d�)#�;��R\$Iz{�>u����Yf���j���[��9漱��J(�n�d<��JK\\��J\$���P+�l����\r�R6�����`��Q[01��#Yd+�]\"�pv؋AL(��Zu\\I�\"����zHDZK'�)9U��[�A�/�J�8�p�L�D��hM�s�t�r��m�4�jS�i�LK�-7g\r3�Y��v� �0��I�\0�IE.^��l�\$@�)��y��\0��='�ʠM�ހRq\\��&I�Uh*I�(Tz5:�Q�0��Gb���'r&��	}4HM��u�I��J�X�@��q_�L��E+���\\LIy�a4�F��=d�c�re\r�zn)�;B�|�2�\$�\0��2�+p�h�\"5b��Y��\"Ɇo�t�-��⤔��I��&�����`�V�G�{ 	�3�Ο��i��5��_3n�M�aBc�š�����c3�L�[�ZwQ���ќ\\�A+�d������%u���,�92>f�i%�U!:ь�`����؈rf)(��(��b&�.T.{[�fl՛��v�Y�Ahm����sP�7���M�kMu;�h��WK�m����	����9ʶ��F��*��Ԛ��'=[�G���qh'j!~���Ъ�z�К�͙�:g���4&��3H�--����C�tj�ĵ���2м���jBl�>���і@�X�=���՗\$Ro��J\$TG�2�/���V�\n��dl�A��-1E�hTЦ�#�S5,���.�H��Ϣ�&G^�S��h:��\"�F!Laj�\nX��C�ig|W�Ӽ�3�Y\n�DF�2!�ǈiר��?B�?qqR�@��Cع4MvF5R�PYAIh�?��\\y����6J��*��3�7�C�֎Y-2�M� 0\\jo�>kB����\$�\\�m�.�Ah-��Nf��4�N3^�w��8�fʈ�cP���-���~d�S?/g|}��i]���F�-a&�	��Eu�T�4T��P�oc�v������v����]��:|��]�Sƞk�k�%�v�У[�Z��\nm,F��'�KK�.U˕J>�ݍ�xn� ��&�m:�g*�9����pC/�x̎���H�� �(�!���4���3M��Fl\n(�N1T�lr_%2g� H�s�ܩ�r��#��!-�����3&�;�(�㣂�L��nS:�\$��[��]QM���!��%E���0@��PU'U�f��� zq;���d�gI�P<��i�]��r��u0v��-���q�]�+�S�߅_����E�ɑ�ښ	d��\"�[�����B�%�v�6��J����/�;QV�/y6�K�ָ�g}I�q�܃n\$����[��Y�t�yW^e7\0q���.��?ϱ\$ɇ�2�~�Ȗ���LuBZ7�#�U�&N�ڪ�(�`b%3���/�)�%k@�ؠ(���iP�,�Gi�,�tV�<��@t���k�\$F�E�����HLq�Ŕ?g2�0&+H���1A�nG���`��|(D0�,�A��Ϗ�����J^'E�G�滄��#^-��UgO/{�t��q����:sp��%�V����nC\$b�\rtr+�}[�LZ��p�6n�L����.��nt���@�\r\0�`� ��f��@p�BG�-�����\r���g,��q\$����g>9b.)&o0J�/��O� �\"צ�\$��������'��\0En�~)�'���@P��Tȱ�j��~�&�\$g<Ч� �\n��`����BC��,��L�[̦�l\$�.��`HK\"`&���&:�v�^0Ghॶ\"p.��oI1�o{íNވf*B��K/�GB���!���Q��C�Q L�o�Y�#�fa!���\"&�\"r\rDtGH)##��#���6�L�J��x���u�s%R�-&�r(�Z�bf��|��u#��'�}&�\0�L8l#�(�s(��q�&��'��J�*(��&B��@�8��K-\":\"D#�R�E\$=.�)��-B#-�#��-�+��.C��\"��	��+d.o��R.�Ri\\��D2Ē&D~�G�#C8��,�r�e�q�\"�/@1h-J��\$��&�VE�e��\0��`n�(L.�C�q:\"�#.�D9f��ϢJB�#��\$�39GF��,+��7�/	�\\-�Ц=S�.E(��;B:��5s�r²\r<��7S��\n'*�8�S��F�J��s�sP�YG�?��?��{ϊ�e�b�(Ӣ�W\n���z�O�#\"�3!�7�����b�CC0�3�I�0R�O>������^V�\n��r+ ���P0���|��Z�(\\I�.��^���JR�3�<0a=��oM��=#�)�V��P�3�Io��ԟ>B�o��HQ�KNX��9=RM)���PuI�J�D��.��b�s4�M��\"Ӑ}�D�d4��S�dԣQ`t�~\"c(�H��O�e;)�P��:��P(E!�p5���#j�L��F3ؙ�S�&�92U<7�@u*r5%�K=)�=rOUE�G2�Q�����T�&�� .�X�XIG�X�P�U�W�U�X��RP/NB��F^�+�2��M�8A�FxO�d�ƴ�Uk�O�.�{nS)cX��,��̽�\n�,���lB&�Ώ���2Ŀ�S4ҫ �c�Yu�q���B�}^��^�3_(�@S�mTt���A���P�	�	U���yMR�fUe	�j|Vn����6vn�Mg�?u\$��[ң4�4�g:�~�6�m�WT�j��j�Wk3�Wt�+V���iV�K֝l�U\rbQK��r�NI*�⁏,�R� Ǌ8����#�U�*c�k��ppF�W��{Q��P��<vx\"�?rVn=Vsl�q�1r\$su��3m��a	��;5u��� Cg�v�s;u��}V�n�-QWDpE�E:��\",�4��W�AV�-�VM��l�B0U�8ֹO6�t��\$�y�1g��b��O*�c�{s|N:�}7��ԗ�{�MWOlt7|L/|��~w�@ױw�p%����~MW}�}sA@ykg�1�^��'��8ExN��-�4xwn�QYp�`�f��\0���\0�@�\0���'0�8N\rS�xZ)nbXi��M�\0�XX\r�˥��8~bq��%0s��R';X�-�)ZU(����e���J^�8��S��pIN8\no�!���/m�uk�J�-�]́u�hxÏ����zO�s���[���B���7��3֌��E��m�n�<)�8XXRq�\$R(	����UWl7�+%'s���Ku/�N��%EV/�D�x�'o��7��1�,-�i8WKM�!'G���Ex�W�K��n��q�O�vu��V��Z}���`��\0�a�0�W�U��*F��\$ta����g:@�!.&9\\c�`3|�UЂ.��\n�d8��\n�0�&�c�c�\"+v�G<��E�k!�ţP��d�i-b��\$��b�E36�`��PI��A�9���B/�ᗧXGL'�H�&�v��M���N��ӫOhQ�N��Fy�.�6+�d�s��}��/F�-�*qE-f�/)GGT��e}pŬ4�[�\$Sf�^�Ed#m�d�=9�\"�m_CK=�?�㱅������:�2|��7��	�s��hsf�i{G��˳���X4�2ݢ]�����h�ii08�	h���ڀ8��x8#��?�\"��L�h�����«`��Dk�ʨO���k�U��:�S\"�3{cO�-bŁt��JC��{iPSb�pi��6�BP��]�0S{��e��b=_����+�6)�8{D�T�c��G\$�}�����8Xc�8beH\r�ۺ/���l@";
            break;
        case 'sk':$f = "%���(��]��(!�@n2�\r�C	��l7��&�����������P�\r����l2������5��q�\$\"r:�\rFQ\0��B���0�y��%9��9�0��cA��n8���y��j��)A��B�&sL�R\nb�M&}�a1f�̄�k0��1�QZ0�_bԷ���  �_0�q�N�:Q\r��A� n4�%b	��a6OR����5#7�\n\n*��8�	�!��\"F��o;G��A#v�8.D8�ܞ1�*����͗ə��\n-L6la+�y5�O&(�3:=.ϐ@1����x��Ȃ\$2\"J�\r(�\$\"��<�jh����B��z�=	��1�\rH֢�jJ|�)�J����	�F<��\"%\n�<�9�\n\n)���1��P�����)�,`�2��h�:3. �-\nn9�fR���<��ʣ3\r�4B��@P�7���[0���\$B���e\n�;\"�@ؔnC�\n���E��X�EQ��R# ں�*l�R�V��R\"�(��C,Q\n��`@!���3��:����x�c���>�Ar3����0�;�c ^(��0�a�3!l�j�x�\$\"���Ud���C{���,�:��B��KMBx�\r	���SI�x�'���&h��5��*�-C���\0ĳ��N9�d�E���ˠ��!t8�!5B\\�����8:�A\0��L�qDD�r`�B���7����x1�0�7\rm�� ����/o�\n_/��5��*r�����.5##\$IҤ8.bL4��\\�b�B���櫮�Y�Љ�c*`5M� �S<������r,��&��.�Z�-�t�=`4t	8a�w2�'��VT��p���]Ķ͂{HP����\$����\"�3���n���� ��wx�w��֥�F�5���:zo�c����-B��6/ƣ\$7�\0000��`�.e����\$!<2)�B�Lb<���D�Kxk���2��0sSA�[&BTK�@`b��V[<����[`ϒ	A@��2�Qp\r���.�h�M,��R��Ji���C&�\r ���&�����#g��\"�h���t�p*��L��*�#0�����i�5�:\$�/�5 �N��BU��B�^+倰�\"�Y)P,՞� /����@�%�>R�͘�ʺWY&�^���RP����|��S`U�����*d8+P�\\@����F�NΚ�1\$�T�t(�N�jR����n�d\"�W�a�U��J���89-���1�A�m�׈	�*RzP�@���d(%[\0�r��#2UI\0�*���bN�&^��y`o���bB\\��]13ف�ihzY)�d+H!�:�2��?f6\\t4�QtER�\$%�7w���k�Sa:=�9&�h)�2�*2�	��@���jrLO8 ��,D��UjX�\0�R%%!f�U�&KI}P&p���,�3jԺ�#�����\\�!4�yVƸ��2N-���GL�\nӜ]S�l�*�� �LA\0C\naH#L�q�Kx(��9;XvC%�!D�3���#j���䐓�U=ȑ�Ko��[�ď<�G`^���Z�~����nA\"�8��ȶ\\�ȁ'G���X�6�d���2-�zTK`�j��D@�I��L��(���@�����h��|/�\\K��K{L((�30�sm��m�~���r²}���)�\"�	��v _%�TF��K!����\$��Ɍ�H\"sD2�!�[�Y7!�TsLic�MCT�֚�bl�	�\r*\"�����O9b-�T�����xN�q�����G[кo�U��ě2Y<'�6S��ڙ��/%4�L�S�%�:ϯs�����!�� ��4�!Wt�ނPi�q��΋G���C��93O��J��I�٢PD��I�D!�`t�J(\n#�z�N�y�pfؚ�s��0\n	�\"6���;��*A��d`�ь\nv�F�r�c{��SeM-z�y��r��� М%�#4&g'�d�0d��}��M�Nnl��6_Ӑ]x�l5\0��jI�`FO8�Ǐ�� �1�]�k%�/M����U�CcR%�3��E���\"����P��O��ݖȉ�c)H\n˕ ��X�W�l\n�P���/���.;ǔ��L>��*@��A�ā>D!���k����I&_�x\nd`��233\$�['3��w��N\\.rѝX�p�u#i��z�:�c�YP~�%�Z�G����x˻��g�%�TX[叟��6�6y��Pt�\"`6�4BxK��ЦfT��AZ姹���S\$گX~>%�d}����Ww~+��/��M��_��R���N���*lM�O�N�f�Yc#�\$������I���~�+Y�=�OԌ��\$��Fb|V�%�<����m��i,v0>�/�`b,@n�I��^�h'����?d�	lg!fK�ܱ�8��9\0�<��'+�bb�i�>B�.�2%�o�l�̜6J�0w��㾺����7�\0��Ҥ���\$-�	�|%���pu	���C�\n��P�*y�pM�;��\r��z���\$�ܭ��d`- �,�ʁ�T�ܦ�qD��P̆�'nRO�i��g��̫�2\n��\\N)����n'P� ��\nK���M=���mU��=p�sP�D\nK��v��v�g\n-�\nk��|�tz����t��{f�lD4���_т�Ѧ.�l�qv��c�\$�+ѱ�Q�Q��/mg��ѪI��B;�%�������\0��z�\n��l��<�ч�b\$2�\$R�N;!�\n�!�1 �\$9�Qm\"���`�\rF L�j�o\$���Ę��!��'����HU�bdM �V�Vc�P�p��e����j�@��xl�����N)������%�F��Ƣ@��2�R6����G�u(��(f`fD�߆P�(�w.�Щ\$��2:�2>���0G`�R�8��#��/�P0����\$��)	}�\"8Z'�}3�B�{k&��f��f@�#�}�;D�U�\"��ƪ\"�@�0o1���2���81��\"�;9�3Q�3�'��jgaӦ��U8E�4���j�j�\rE�����#�&�tN@�&�V��l��6��#����&3�2��9��-���S�@l�SKA�Aq,UQ�8�1/gBO�:4,t�Mi;Ñ=�\\��1�3�FCqi9s,���D��3q�C�OENT�nk/tMB��GC\"��G�YG�30t-;\$�G��DI@Gd�1SD��OD���'Gr���OdO�9�7J��Kί044~�6#�C*΍�����m�L�lO�4�U7kddk��o�%�Z_�t��c��Bϯ�\r����;hl\$ �#�1�FI��'\$�\n����O�x8����2:	�'��\$	!ĸ\r�V=@�\rcPDcV��r�ct-��@��Do@2Dd��\\�2�:hnL�f�c���@\n���Z���f�m��\r���[[\$E*ո�u#�x��Y���G[�rЬ��\$!�D\"b*�IXj?@�}AJ#d��>Ↄ�n�T��M'Ȏ�*�Q`�;��,c����L�(�+�c\r��(�C�\nq�j��\"J�-�H��&��e�#�?i�-�����t��g�;g�}��i�s*��-QuB��h���F���޵��V�0�����F2�^6@;��q�B��'�6�'3)* �u�ڠ�>B�t@�P2g}j�aC��Tk�2`�%`�1@lG}b��]�~-�&����3q&�\\��-r�f֐����=͵?��=ࠇ`ܮ�c#�ؤ� ��";
            break;
        case 'sl':$f = "%���(�eM�#)�@n0�\r�U��i'Cy��k2���Q���F��\"	1��k7�Α��v?5B�2��5��f�A��2�dB\0P�b2��a��r\n)Ǆep�(0��#�Up�z7�P�I��6A�C	��l�a�CH(�H;_Iу��di1ȋ&��a�C�����l2�̧1p@u8F�GCA�9t1f\$E3A��}�k�B|<�6����?��&�Ʒ_�7K08�ʱ����D�ы*�P�IFSԼU8B�ҩ��i;�L�#�.}��Np�!�7��������c�2\$B��9�#hXϿ��2��:V7���(���@����	��T��<ˌ R~:�sj� ��Kx�9,@P��\"�Ȏ2��h�:IDr�<C��\rk��86\r2<�+1�|�\rn�%\r2c'��T~9�Q����JT�����\rH�)52H�2\r�{�>�K���i��1�l�7�V�>/�@;��CA+p9�Xx���(��C@�:�t��T6�ʈ9�X��{�9�0^'a��926�cH�7�x�\$N��ʫp��B�ޡ\"���3�� ĵ\nj�#�����!6mB�	�|5qO+������Ē�M�_�R�_�\0ӄ`��#�`�2�`P�'�B\\�.�����,�ɼ)2P2��\nsS��6&yH�:�6s־�ë�1�S�2C���n�P��i��%��4�+n�\r�8&A(�R\"\r�e�9(*FX��y��63��0�k��7�&-c\"�n4sp��b1�W�r �B�\n��Sho<;���|�:+�;\r��F|H�Ůk��\\}�\r.�o4�Rt�!a!�C}����C�<7A8�ջ���f����(�3%�\"D'��Z0���{'b\r��?��%3e�\$�w�\\��0�j��up��S��ZV:�x���\$B�y���v���Zn�4�n��qS�Q�UN�UZ�OJ�Y+@�ϩ\"�]��B�TB�Yo\0�r�Jl)@��Y�-!ش�����\"	�ȏ;un�TùJ� �`��T��T*���V!�Y�P�����9���C��]���p��\"3�\\0�h�[�c�\n �zJϜ/W�D����;U����e��R\r���&�BB��3DR�T*�|\r��U i�a�!\0�^��i\".��|}M ��x[��\r!�K���H\n\0��RC��rs�7� ���\$���x�s2P���p����xw������Q��\$,Y�,	� ��9�����Ԫ�R�T;�P�1�gTRV~J�\\|�c(.�^��@rBS\nA&�c�nH��,���B�k5��vFLJHA�Dᤉ!i�NK�<&�Ib|Ȣ����xr��8�`�f�0�c�\r���7!SI9H`qf��c�C�<��\$1�%U���3O҉�`�f2D�(�\"hZ{��T�L(i�5�bc��R!@�y��\0Sǡ�\$�#n��!m\r�&U�hB	+�Re�*�gw��}6�\"L���#�܇V���PeI��-��Kd��hgZp�g!ɮ��t�i�5���\"����HM\"i\\��{�F��]̦��M��c��r0}K��&F��V���S9\"E��:�aj�:n��w⑻��a���(�o��7R\0�\nͻ/Ǭ���v�������2fY;Z���SÒL� �A<9�e���b�W��:#0VJa�����]ء]!h�\$g;h��NN5bD\"6��0z��4�P�dB�/�S\"��UNi�pf̷����A����W%�\$�t݂hn�Fsp��7�2��v�K-���D�_Z�� �sF�İ�c�ac����������e�к\0�����`���&�i5j֗'�-@�R�v\n�P �0�*�7>�&���:��(D�h��L����1��S�Xf�\"��l�m�v���:p/�}���0�rHd���x����]�b\nP	Z۷s(���C+�K��g��'����.�m\n��Av�\r��@S�?�8�����o)N/�%��B�������#t����(a����M��y\$�#ոL|O�&����s��`�2�G��q���H������9F:����5�*Sk��O����wQ�݀��>ǿo(/�a<�3�яm[\$���D_M���5�|QT�d夑���R�/ke\0��9�f�� ��%���f��P�t\"�\n1��E�k-�*A%a�\")�j�䍳G0�(\"����\\����iV�b�&p�����pP�Ɏ��c���e���9`��K�J��yvS���?	+��}�e�+Wv?\\��l�V�,�->�\"��v��Z�\\FB%Ţ�\0�,\$ˠ���\0��d�\n���k����A��h\\��I�\$R��\$c�����^����D����Lv,/��pT�j��R�K���t�x��\0��Pt5� ���Bt��\\���%��9(^�F���8b`��nvo�ͮ�0���!��к#�����P��n 0�\n�\r\rp�\r�\r��BCc���,0�������0��\r�����l��ɬCy�Q	�}L���:�g��sp#jg�r6k�bF(!#�6��C�����	�B)\"/C]#�6�\\α뜘i�(%Q��`�2h\$=d H��qA��q�%o�E,&o��7��b��\\��q?%����1�pq	1L��@Z1�]&���?�����)��2�\n�H���g>��K'=2%!lp&N(&2X�����rk���D��1��oOX��!����W �{!e\r�\\���!��#�gd�pM��g\"�k'�t\rfyq��1��5Β]�0���\$��m��*� �R	P�*ұ,k��q\\#����zr�+��b��q1&&-��!*��)��\"��m1�䒩zh�bd�F(�3�엢\"���0�0�\$�z�����&`Oj1��Ҥ���(�kw m[-,%�=&�O6S/ry(n5�\"���3xK!��� ��KMq6H_5d�5��Kso,�� d�5Ĺ'0���ɂ`/�d-E�(���b�z\0��G�cN��P��cڔ.#=B��n���3p�X��znܜ3�A�?S��n��\"�E\n���L�\nrN?�x_�;=bH���(�D��2���6%s�CB�C�}?��DP�C����u?�D^I�H��ki6I����\0�8v\$@���	\n6��\n���pvR�B��/'0��0����ԟ ND!���qnEJN�\n��Et��\"!~!�4:Ŋm/�L+�/�� \nB�Pd�m[Gi�<���#~dOd��2^��-��!�G\$@�\r�/M.�E5\"6�be# �8DRb��mR��K��N\n��ULmkC%�<�@�4�K�`�m5V�OU�ah381�#2c��.O,�1�Z���\\��h�Bd�pZpM��\r����\"`��Zf[j�G,�9��ZD2��)�(�@�P`�E�	��X�SA�����\"�^�Z��0\"��BH��콕b6d4h\0�&p/�a\"�cUp�ʐ@�2�=+��L\"����BF�\0�H";
            break;
        case 'sr':$f = "%���)��h.��i��4���	������|Ez�\\4S֊\r��h/�P����H�P��n���v��0��G��� h��\r\n�)�E��Ȅ�:%9����>/����M}�H��`(`1ƃQ��p9�Whtu��O`�J\r�������e�;���ьF\rgK�B`����X42�]nG<^PdeCR�������F��t��ɼ� 4N�Q�� 8�'cI��g2��N9��d0�8�CA��t0��ոD1%�Co-'�3�Do�8e�A������Z����A�)�@�{b�0*;p�&�\0��\r#p΃4���\rY����] �s(�>�X�7\rn0�7(�9\r�\\\";/�9�� �踣x�:Äk!���;ƣ\"�N\"��\\���:C�*�����	z��E�<�E-����¶�-н���\"�#JҐ+d���*{�^@���5�1D�K��0j��F�9A���h�uPڬXD��*��*L������@2��^@-�8��R6U4��5�z�'QƎT�8Ч��V��������G3R�D�=O��i1� ��l+��Hc��#��1�#�*3ݷ,r1Gn �4���0�T�9�`@`@�2���D4���9�Ax^;�p�p�q�j3��(��#��&���\r���\n�Ѩ��#x��|�2\rGYA�,¯*��77ҰeýM:�	+YJ\"o�VˡMƦۢZS:���'O��ږx�0�Cu悄�\"\"ѽH���i�ѻ�M��=5���(T2�_�Mz��0�1 �*jSO1a�=b&0��d�;#`�2����6f�H�#�I�K�CD��j��?�3�N�%�Ѱib\"ˢp�O�I���2k+Cf\$L4#L[/_b��E-F��u2��٣פ6�D��*����z�k0Z������&J��S/k�*�n��\nT�����F��yY�9������j�)��)I�\$/�L�*/�#O� П�hM��`�k!p�t���^c�k�&j�ǶvԂ B ЉB-S�]:A��\n� F��7D�b�Q�����̓�[&ʸ�\0�<��]�.)`�[�IO���;3\r��X�Wz��t��Шs`��9G���\nI�. �N�(`���\"PJ��)�::�\\\n,:L�9G�䅁���Ƀ8�C\"�a����X�b�e��6<�\"��*�Fj��>���3�tV�X�C�\rR���d�� �����?n���I�.	���J�J��\"1�4�����L�a�A�1F,��c���>�Y[��~h2��C��\r��:M`}�_���f�zHFQ�\$���K\"�J&�F22ե#QC�Ј��7�����.�́�\r��#����m�0�i�\$&�u2CH�`���Ө�0Ѱ@��\r.�0�DBS�0vkT�>Xc������)g�HD( \n (WS\n(+\0�1�hNNZ[���!�j�t���\$�Ӟ[�FA��䂿�Xo������Ա�B6%�Pѓ/:L\09���\$с�e�8I�ɘr�t1��LC;��n��78�-i�A0��3�L ����Ps]K%�U�\0_�\$\$�n�B2)R�V����%zhHQ�ni�5�ZO��@(����,iH�P'D(��l��W��\$����%ŧ ���Ӥ�Ë�i3#@�,hZ�H�1�dyX�2I:�JT�G�P	�L*W�c���RT��?���e��,�;5y�+�q&��-(&�DԂ��N.��c�E\"Q#�<�5ƜR�- %����d`u�3^���\0���\0f����0\0�,(a\r��4хؿ4.�a��c�E^�|Bd��^���YE����\0U\n �@�D�)xQ4)���1�~Ԥ,��N E	�Wk\rd�J[�PiQ��q�!�����7�L6��P4�;�8���3V9���~�d)������0�^E2u	���tN񷊽�kkB�kH��6Pܣ%}d�%tg�*���cŹ\r��7�q�	�S�;k3Ȑ���]�\\DA�h0�������B��\\�)%\\+k\"0��ž��Ŕ\r���;�i�Et��T����0&�e����#�I�D\nú�+P���N��K�#@Ok�4���:ԇ��<eϟ�\n\na�=:K+F\$ä\na��Y�A�aHC��\"_�~����+t�O#���\\p���P�)����b�Ix��A,��^�{���j41H0���\\P3Kw�iD��L!�PC���5�P���}g�a��G<�\nJW����0*���W�,X�d���94)�P9\r�񽻾!�d�y�p�_&�����q�\n�hT!\$R��m��]�D;D�z���g�����m0���@��j��6��2�fʂ\"�K.�A��#\"���B�`��j�F�\r�?�����B\"�F0�>&�D\"�P�juh��N�����`+���cK�[\"MpLR~i������>����pc	c\"���֫�,h�b������]�%OB�\n�}\rpR�iPn4-��P�P�P����m��	(�cT%��@�ѭ F�� ����)�`��X���ez'Jh&�+\"P��F��*NvJ~1�hZ��*�M#��b�#Ac%f&�*���}&���L|NJ.\"���Hv[c*���xeF�x��H�.8�ѹd�x�0QG��Ś��L�gRC'�N�x�B>�f��N4٪�7n�N+�&AC��/ ���1�7.^+n�t���'�Q 1�(��,25R6Z�z4��}�(�r4�-�'�?\$�<��cBV��. O� (O\n��\$��dh\n@1rrhrb�\"�/Fk'J5�&'�H'(Jnt�DQH�[ ��5B�p�TW��*h �h�G��\r�6��YO��������\$e�\rL�.��.���2#�!E���/�U.�Pk�U/...'1r��RA,1+o\n��20�\r3s*��R�24RN���6(K�J\"y��6\n)��j\"LU#�P47^j�pj�+k/0s7\no7p�7��/�81S]8shL�m�7-Ɯӗ��7�3:1*<�0�f���;#�3/�<�y&���3�3�Q3�!sL>s���^}51��Zy,�@���M�ڌ�,���.Zx\nn�\$�0�^UB`�\$\"��g��B�A�*o+�>oS+��<��D� �Ŵ�H}C�^A��1��B.\\h�,3�2���=��>/|)\"�45E��&2�>���Op����F��&����N�@4��T�3�K2ݤ����3%���L\r�2������3@���4�>4�\$,NqX\"��?�J�i:�c�74�G�lhp�Q�N�\$O�T�`Y�J�\$L� ѧFj�h\$觥�\r�#����Ks�5�G;UKN��U���UE��Ll�%oh�K�u�����/L����p������1�r�tu�aX�QV����7�J)��B-�tq��M ��5���Y�NHo\\r�5t�]\r�PK��4/��RI,��K�9]���1��V�_��6�T�B�R�/�8v�fD\\����@��� :J�bgB��Ya�2\rv7bLh�%��s�ȹ�F���z[�3\\6`��eJ��@/�)vsLT��vh\"ug�5��/�h�� Ҭk�\n+���l��d#viN\"�D֬�6uU��J��Dv?�K��25S9'jL��<ܳi6P-��O\nF����5�oM�#�B��\0�nn&��	p���+\$(i���\rn��m3G�30:L�t*�	3�	��tq�T��OwSo����d�ցtނ�*���5gb\\�\r���k��\rR\n���p��l�-0�7���O0;�@�s\$Q9�rlׯ{���jf奙:R�-H��䠚\r�@��f�,�=x�ٴl��Z����[�w����7[&��#2kjx! �o?P1��L�X�b(�}9�Hl#@\"�9E��\$�am]���g�t�J�.B.;���{�z��c�rH�t�/T�g�r:\$�\\J#7H42��R'��8�C��R'�{x-\$%����� Ġ6h��M'�@��ߓ��-��D����ڣFhq�,�Z�A�\n����\r�a�◊1�=q��A��JN����^�Y@Oz�y��Jı�*�=\nh��I��`�H��ҳ�S�V\r�*�;UӊY7�#��̾5�<y⽄���ǌ�`";
            break;
        case 'sv':$f = "%���(�e:�5)�@i7�	�� 6EL���p�&�)�\\\n\$0��s��8t��!�CtrZo9I\rb�%9���i�C7��,�X\nFC1��l7AL4\$8�u�OMfS��t7�AS�I� a6�&�<��b2�\$�)9H�d��7#q��u�]D(��ND�0�(�r4����\$�U0�!1�n%�(Ɖ�:]x�Id�3�O��\r�3D�pt9�tQN��������!����ݾ�r#�-�+/5��&���d�~hI�����':4�Td5gb(ī�7'\"N+<�c7\"�#̋��죦E#μ���j(\n�\$Cr�ů�\nL	è�6��3C7M�@�=��9<˫�!\"\rh�8C�����*҄3	#c��<�H�<��*�)����C&���p&?�,5ñH�(,�lD��(��4\r̫�2\r��:�/I���8�LD9���]�!��>JU\r?���\0��\0x�\r0��CCD8a�^��(\\��#s��zJ����;�!xD��l��Sr`7���^0��z6\rMK�\n�H�Fc��:�¸��򭐖���.\"p�/�-������7`Aw\"H(�7в�&W�O8]B\r��6rv�F� �:��R\\�c\$�95Ve�5B0�7ZcM��#8��.���)�O\nU+.dv)��3��X��2�o0����<����d(腧�F4�E��6c\\E�9B���kL����,����mn[�Tʂ��\0�� ����M��4m�%9��t��3I#8�	#hᘹ\"(���Y�����z!9[�,�tj�A�ء�	H�d0�i��5>B9e/��C��;�x�7C0�UI8�4#�62τj�#��d6#��)s�E�2ac<[N	l�����\"�M\"7���'�gP�O���S��9zC3?�{\\���8�C1�2�kA�#�� #�^S�4������pAO	�����!cf��2�:hHJ�\nd2��:��\n�T��S�5T_b�\r��2,\n��>���0�%�l�*�c� ��d-����J����PB\$� :�`�\":�b�\"�5#`��P�)���Tʢ\n�b���)1\r[+�	\"()�2�F��B(-]�=EB��5D%&���DM��7�(�D����J0XܺJI�+G5��5���T�1��3y\0FQy���<��9}�<ʇ3H[�k�.m����zȌIAƤ��\0�l���7�}��d)@���fk��H.�`0�sơ3<Q\r0˙�6L�q��@�D�0�\"AГ���ˋl�\r�=�K�?0\r9���u���҃#!����\"�a#,d%� ��nQ\$�����FL(�C\naH#M�X�0 qa���xC�8���E2TK;���O�.��#.���[%f�f\$Z�@�J�T�P��{p6{�@�M\\��A�\nR�+*�J�ΜJ(�,�	�I��#h�(�j�AUG\\�Bij2*.:Rk�Xv4�f:�����\n@��gzG�y7!�M��D �i#�*Zs�������P䕑Fa2�	��FN�Y�,��'`�B  \n�@(@�(R	!8#�ːO\nAP*����^����2����^b�Su=κ��v/�\"����2�\r��w�Rv��ݤ��(��|σg,�^\"���p0JJX�%d�����r��(#w%�1�Y�N�\"�ÑYsU:!������ΚJ�	��\r���4GJ�_f��v�Nu��3v���\r��Ĕzg�(!-�&�NM\r�.:x�e���Pq�����\rAΫ�c��HaqZ���ߚ(�	\r�ko07>���tyD!�l�s�<A�����>�Q(;N�]>�\$r�\"'�	��T��_\0�����\n����c�� r2a��]���E�3���rqa��J����Z�Yk:�\"�m�EW�1�q߽M�Y��5A��/-�{bV}a��>-Ͷ7M5�{�q�'���ޓ&�5�j�\$9�z��om}��w��^\\:�l����	'	f*��RH����N�I�����Դ]�O�%c��K%�ty�Crtd����=��f���nz(u�����A��;k5�]MU�Mpa��+/Y��Չ�Af0�5�氭�6������N뤟94�Ē;'�������^\r���c �����d����0ɹ �y��d����J!�f�IL&u�����<�W�B��/��|py��仭��\"�M\\^~S�1��]�޳>�	DH�ڽ/��>�z����s*���Ӹ��~lܗo��)����K�ܘ|bԮ�;��#��?!���&~�ݿ����;���j\"myM�¯��O�c/���,n���H�f�xэ�:���ϠZ���)�i�b�\"�c\$��l�BI����K�|�ظDL��.9�@=Ì���J�j�'�'\0*��5��c����E��F���\n�O��Ř\\n��/���[Mtׂ\n�j�p�N��N�p�n��p�#��M��~��P�v\\��?,�\r\"�5�Ji�4\"�Пml����Q���Ў��1�p�[��F8d��5.��;���6c���� �/�+f�J�����d�\rmx)P�\n���r�P0�p\$�uХ&�>��)1T&��b\n\n������\n��1�\$����E�����m\"�2N����>/�O��>e�D.O� �N�B�(����\$���e�:�#�-8M<\"qw)�!2n�!�JjC��푾Ԓ�͉R9!c�\r#�\r�zK\r�\"�*���!.���W%�%H�%�4!/I D���&�\r&��	�2C��FO��D��0e��,��_���+���ZVC:²�Q��	d�l\"�%ʔ]�72��%r~�('.%-BƱ�}%����U�f\r�V\rd���QC��\"&��@�16�.P/px�j>�+�\n�ZLփB\$��\"�*\r+bq2'*�E.�)3�N����g�P(\rG1>���^C�mFe&C�Dl��XhJ`���,�bwNHE�Yl���I\$���Z%bD	|e��Gn�+r�(k\$�1��§n���;3D���&���q���!=�\r�3����3�l[D�e0P���s����pn�T�d&K�\rl��,�6=3�9�l�<b�Jd/í6��;���\0P�A��AF���S�q@�o����}��rGR1�A�F\0U@�";
            break;
        case 'ta':$f = "%���)��J��:���:������u�>8�@#\"��\0��p6�&ALQ\\��!����_ FK�h������3Xҽ.�B!P�t9_��`�\$RT��mq?5MN%�urι@W�DS�\n����4���;��(�pP�0��cA��n8�U���_\\��dj����?��&J���GF��M���SI�XrJ�΢_�'���JuC�^���ʽp� i4�=���xS������/Q*Ad�u'c(��oF����e3�Nb��Nd0�;�CA��t0����l�,W�K�ɨNCR,H�\0��k��7�S��*R�ޢj��MY`��,�#es�����r�ʢ����\rB������B��4��;�2�)(�|���\n�D�����@\0P�7\rn��7(�9\r㒐\">/��9�� ��;�x�\$��9�X�;̣#w�I�@���k6�G�\"I �uW(��R0,d�����\rØ�7��j*+�]�!1��%�n,L��k��\n.�uHY��3V�7drڱĪ�\\)�Kz��0\\W+�����q�1ezw�v�櫖�J)���ӮdB���H=�Ͷ\n����Z̫��kF����8�7��-��8l���2�=u@�)u��L�WbDh:a	�;@��@�<�o��rR\n�h�)�R_���9d��M����tFa@�6f\nM���i�lƪl\"֫\n�@��ۓa۷��J*4�I+��qj8J��ښ�#A5kE�y�# �\"LA�8;��:��\0�1�oU=\"��t�1Mn��4����0�3��9�`@q@�2���D4���9�Ax^;�rW���]2��x�7���=ϡxD��l��ȃ4�6�4���}?�M�V���=���*b��Z�v�����±�S�X�U�U��+���c�0�_�Y�䀫F���@�2x�2y���.l�P*�V�-	�Vd�ҐW!�7\$@�AA(dE͍v��#޸�z�i4��W[�U�5�\"�j�W�`����=�s�ͅK�Da���v!�:�WƵ�\"�8/8W�u���na��T4��̉`� �p���;�T��A��A��e+��`��:�i-ql�R���H�'o��Ƶ�\r��]����t]R�}KU\$�x��{�l�9b�\0�a�<�1�ܐB�L%�z3�YH��F�F\"H���sP2�ƞ��B*GP����+�\"N~����Z��Cꗏ�_Fy�g�F�:c���\0�r��q�	G�d��L+��Y0�3�|��.cCW�)��x�u��H�t�+���40����2���VP(	&�;�Hi3!�ѡ�tA�O-_���RM��ו\\,A!��4Ltŝ3V#��z��P��I���S*a\r�ص��r�C*�ujyP3wߌ�+K��\0���Q�;,����cl7fj��J�\0PT\r����\"�so\r�3C�@JC:Dn9��hx��:� P�M[�O� YH���QM�Dڻ\0j�K�Ej���\\=n�9V���it���:�C#mreɹW.�\\۝s�Ѷ���S�M��**d�۽H���\$-٢3K؍5�k\0#s6el8�Qg� Ar�I\n��M8�UsU~�1H]�Rjk>Vu�;�C�p\r.M?8�#k\\��sNq�:\0��U�t��Ժ�UP��vN҅��D�å���0�n!��'��Yp�Ʊº��ecq��eV�y&��\n;B�zs��v�a\"���{z�����g!���J���l�����Cُ a` �1�L bF3%jX��\n*\n�gDW(�bC�z:F������.�Kq�������,�g�Q�\nj���8{�<���X\$���t=g�8�y.þ�?���5�r�(���>�\r�49��6a1;����yo5�0Gp��}q.��� �}Ch¡�3���|�ĝ���0��1.6\n�W(O���y^\"��%�>�WlWc��qAd�a*��y�6�D�O=-�`C����o�RX��V[��Oh��N�6�~6Q��#eg^�	@�Yˋ�-1�V-�2EVk��O��w�Y8-�+T��6lt�I']�Z�:A^>��ɇ��A\0fL����Kgu�`d�6e}8�OI�D��U �;`\n<)�G��[,�V��<�(ѱ���w3\".�����1�/g�Jɠ�7m�C�bL���]w3�˰��t�ӫ�\$�`\$䤕��ų\0S�='J��0T�2z	��k�s�L�&_`�}�Q�U�OU��K�GD��o/0��\0U\n �@��8 �&_�����z��xܥ��NP��Ά]`&g\n/-0\$Ŝ3��bF��+��~�+��Q�Χ���|kn:GD�*X/([C>�!Z��a��i�.)j�jn^0��ߤ���h�Dj	���� �Ȯ�px���)�Cy���bb����H��6�Dc�.�����w�Ԍ�8�\$�G���Wpg	)\0�қ\n<��(d���\"�����0���\$+K���~g ��l�?C�d���Vj���\ny<?\0��cN�V�� t�L��٠@����*�w�z`�f�ɸ���\"�Î9N(\"J\nG(r`�\rH�#p'pR�� ,��G� �\r ��4j�����Ίm-麤P�d�\nV��\n����.;��,J\rȄ�(\$\nm�4@° �+��\n��\$<���)�*��)�5g�ЭHL��^\$��\r�R[�̩\"��mq��)��HdI�'�N)\"�+ �\n�I�O�Z�j7\0��^`�ǀ�\rb\ng&>#�2B��ݫ��� 7�V�L�����pD��j�\nRf���`��3�b�6��!�D)!AFm	��r�v>&HQ�9%���x�0���b�����1�	,P+�al���j:F�#�O�\n��`��L@�N\n����\r0>D��h����:N�,�\"�B�-ÔH.�(�P�R�-3JCÞ�)�l����i\nK��MV�P;�X�Ђz��-'��30�g�|��6R��ˮ��1�7%�,�\n���\$n���83iA6�<�����\rG��(�ZcF���P��ҤU�R�B\\�26r�1ؠI�\r������3p��-1H�5R^��9���S��S�3�/?�d�s�?�G@3��nP3�\r��E9p�9�=d8@�����BEf�N,D���r\$��6%BX||�&PI�;���KFbV)�ATU�b�3g@f)	M�<��3p�;؎�^8��2k3dZ�3@1ԩ�<���EjS�t'k�#����F�%�s���i�|it�@��&	�Is]H����┐i�V���Sz��@��&\0�<�d��lt@C\n5>�|B0B���(8YF�)Q���.��:�w.(IN�T�b�H���<8i-�]UuJe�dM\n�����@�1U�J7qa<T%�6��A7�eYiNW�M>l�~��Q�Z�g@���շZO[�GTp�Z��\\u�W��]s(t�Gt8�\"��!<,ԒnW,Ȟ���P��̇Ȁ�-�L�y\r妤d�\nz3'��Sk�O�5\nuc�t@�J�4#K��*0\n��XU�F���6;;V[��0�[��7��AU�[PfC3f!�'��\0�,���-�_�Rc�?C�.�T�{W5�g��S��q�1_2�Gr\"1�3WU�^V�h\r��v���`�jAk��^3j�ʖv����mU�Cּ-5��B��Yv�Vw@Wo��lts/��䛔�B��U��q\nM�.���;�+p�%^�Cq!\\�B�+@݌OGN�O�S餔��I��bC:և4wg}me�^h3K4���67iW3��qs��vc�w��4ׅ73��zw�����K\$m{S�\$/qF�&�B�m�z��oU\\�ܙ�&�Ox�Sr4'8V�}��~�]u|���Y7�t�!����v]���n��g��~6i e��7���PG�W!sկ��\n��_�Q��֨��Ϥ����)I&�UP뤜��=T+�.B��Z��3�V�.�4J��cW ֲ�M������Ϻ���YZ3�j�j��`�mi\0�\n\0��,�v������>8�Pʕ�X	\"�\r�hW|�9^��!Ig�m7��(�x-;I�,��n�D\rU|��;��u�}�w��W�w��u�ϓ�Ɂ�.yA�8���}���@�1sC��Wr����ҿ�@P��\r���5�w7U�43��]�)I�f����! ���?�WCr���fM_�×٥�L�n��y�Wx+�u��z���9��\r�Y����)5'��]�m�� Cy�%\"��u���H��ЉD�W����y��OPE���^f�\"{y@��%vY�z4�~v�9�m�@��Em�3��\\��v:�5Q�E�5ۘ�������F)]l��t���Syg8B8Z|��A���q�J*gH�����o���Ek7\"⥸�h8/s��u��W�{���u��s~�Y�Z۬w��:���P�zwm�ӭ�w�E�Z�����x�.�s�	�4�����&�v�\$�(������פ��zÃ]R{;�����%��ծ[C.�>���;\$���Vm�&����C��țv�k��M\$n;[��vˠ�\$�(�m�P\rrU��EAv�kYW�5k�\r&x���o�7	����\$}����x9�7n�Oz�I��e�M߼��y�;�ZX�z��ڳ�{#�i^��R�P9�_ĳC�i��F�-���Ӿ�1)�a-(�F\\.��2���|��|@�(�<KzOd��x�yG[@�Mlx�e��U���S(ma��)Q�j�C����\ns.��^y��p���(��uyZW���c>��ʹkJ�F�|�lT-��41V�\r�KR�A��B56��/Q�Qnܯ��\nZ_2s��Y3V�/�Ų�g�}[��o���!�\$c�T�4ͪ\\ �n�\r;������*\r���p���J\r��Nl�׏�\n���p�KL�)��[���ן�%�u������q�3���|&͝�Q������<��j<=��5\\�Z�	�t���bj�T��ݽ?�/q��\0��U{mz=fx_���T	~'z%��P��Uwz/�9L�S�Yqd������<(|�W��V)\0�F�Nrf�?\0�V��Eù\$��`~�š�¼�p��Ϩ��\nŮ�\0�xI��Qٮ�);0ѹy^q�uՙv�	�5�Zٽ��E#(eSzp�U\"��6X��d�zC�U��chGY���M�*¦��;C��D�\r�ID��uչܓE>ا�(����|5l�G^�����<���J�U� ����(\$��H�،����Ø�Dv��:��*FM @�&��3v? ���\r�{wXy�	��mqnf=�.N����P޷V���R:�\rVj��Q��l�n8��\0�^�\$�I�����4��Y3%|�⩽��ye�����^����e��<c��Z�n�5\n5���y�𿒘�a��Z��n�ܕ4	\0H�@��)�L";
            break;
        case 'th':$f = "%���OZAS0U�/Z���\$CDAUPȴqp������*�\n������*�\n���W	�lM1���\"��T���!���R4\\K�3u�mp����PU��q\\-c8UR\n��%bh9\\��EY�*uq2[��S�\ny8\\E�1��B�H�#'�\0P�b2��a��s=�UW	8��{��#+��&�\\K#�[��[=��-���O5�,��%�&ݶ\\&��T�J�}�'��[�A�C��\\�����k�%�'T��L�WȽg+!��'�Mb�C��� �ɼ� 4N�Q�� 8�'cI��3���@:>��2#��:\rL:#���-ڀ� �����E�M��˘���a9��~��NsL���^\\.-R\\��\"��C����CEÚΩM�R�:�����()E��<����)�CH�3��sr���R�7�!p���b�L�B��5�ø����7�I���#���|���@9�Ä�C��;�\$(θ�(��34��#mSA�Js������ت,�p�A\0b�)��>֪m�/�:�\$�J�R����\n;��~�&�u�U��*��9l�\\S,?#�N��D��N\\�M��GR��\\��Ə�6�\nH#�\n���j�&4���ŵ̝{8����R�!*�����L1	pNY�52�-SR���<+/օ��\\�f�)i��_H.!�؜ϊ�8��؅P�'��V�ŶeJ�)7�z�)�z���x�4�/����c�W��zF7���Ȣ�R��2\r���P4�CQ�9P��1�#��3��>S��;�0cݶ�u 9�`@n�@�2���D4���9�Ax^;�pðlS��3��(�ѣ�H���\r���-����#x��|�:R�J�3�����+|ΩɃX\\�铎�TKS�{a2���I���7=n�z���f�L���Ñn���W쳺�\\�;`P�0�Cv�9�A(�A\r�!씂�#ȗ���F�1��H%	��#ʜ�#�.�IGgu/4∅���*�d�!�hO�	݄� GC�lP��0���W��]���>[iLd���&��h��4b���h�#2I�����N9Zd�AҬ���0WEᄣv|(��MJ~D׼P���(g�t�J�X�'�)�2@�\n�I�(#C؀Љ�P�0�� A`�N��D0�ߍ�m��D����Goe�dd[�k_�����_;�f^S\"Q\"�@evd����.%�aJ�\\�5`x��-�	<YxB��B\"�t��QS��;C��h���}*���\$^�hB�sa1�t���DA�\$�@@��n��\n{�)�C�\n\r���,7�P߁�P�9���\\\\���66\$(y_�	�1�:oB�o>��7�@hum��3C@xgKA��@�I�g)hS��n�(0SE��#�P������2�f��xI�T>)�;�[�[\r~��W�(i����\\{�rnU˹�7Y\\�rt�����@��;D���<\nm���%ѝ+�Xϫ\n�.���eZ�j:�a\nf����E�:��i'�5? ���];�N��8���;���9�9'(��wsNp7'P\\����\n�X'VChp?���K���o��T\0��z�N���Z��Γ	��eM���%lc��2庆;\0�|\r�1@��j���3Z�J������S\$��b���0�@@���\r0փ��AN��l�P+UFE��:��X��.�_R�;� ��\0�� ���,�tp�G��^��*��E'`(!�k�}��>������_�k�	(F�~�xw�Rf3�`v��\"F4p�^s�V���@M�9���OS�u�8T7���6bT\0�܀ ��H�H��]�n8���=�B\0C\naH#@�j�S@3�џKr��Z.����Z,U�N�l�	N�F� [����o@�Yh[\nB�K��B�d��6����#; ,��*a\$MD���2�'�\$���+V^P�� ��Ì7j3'p�X�#c�I�1�����@��De���#���d-c\n�E���-\r�4\n�2\$K�����\0ڪ���d�A�k�P�p�?#t��3q�������'�����恩��n�+C�Y.6V�`��dk�\r7EE��ٻ�r>i�G�NS	��Z+������,J�L95����\0U\n �@�ٻ@D�0\"���\rQ����x�;ư:�@՝Ӭt�)�d��{��a�	�B�a��6�`�+�����|�#!=T����G�b���p�9ll(�WD��\\L�A�w�����P�\rIP��g��W%�{��!H�H�Z��W��F~��bg�2~�>�����̇���@��o\\��\$��L��J�5J�L�P�Gbdr���w�\$��E�l����@f���N&2o�.�|'�D�i������dB�+�\\�z`�\r ���*��?����7�i��-<���Dt�B��dN�6��:����5 ��.���\0(\"\"�Ĕih5�^�>d��x�~)NX�Pd��^��P��._�D�d��H|讏�2��d�N��δx\$v^��� �\rg�g�/@�'����8��:�Bl�-�\"�?@�@\nFEH-�*)d��i�MVx����]�}Ɓc:��9�.c�,ׯ:���%��d�H<�VY�TSN`��\n��`�\r���l�mE�n��Mh�~��p��F����^0�~D���jú\\FTFBe	&yo�Ze`X���^J�c���Z#��da\0�n�Q�-�L/��cFFDK�V�y�3�\\3�֏��-�N���B����a��h ��f����]?p���6�g\$\"S\nd�&E��ij�l�~QhW�vB�69��c��4d����O�ln��P�(<b�A'rJZO4G�2G�%�)���ú҆@:J0�G\nMtyB���p�r(��ª�2({�V:��&ID��,�R9�W�p-��d���Q�d�C`H�\$}H�.0{��\$�\"'����ԲRjI�a0����s+0�13%S2��/F\$&B~�s,�E5/����5�Q�3sb �(d�Fsl9�!�|����/p��U�㍮��p����{-I�)��T��2�~yH���&��	:��~<�<S';N!;�p��b�\"�5P�{iO�^D�7e�8n ��	Њ\"�4���?s�g3��q-&0`�g�\"3�;��-�|e/�gqU�?�(��^�\n�7?5yB��	aq��CЬg�SE��\npV����\n�DI��zy�6�+2�t��;!0��T2|��K�a13e9��Dl��FVƀ�A3ä�3����1��\0003��Hb���!�B�Ҫ��b%�\r�QBD��4�6OJ�`�\"��rL�>��FeG�Ő����0�0�u�oQ���..��M	���]=o��`���Ͱ�-��@�5C�n:G�J�3�:�E�8�s�\n��u?F��E\$_H�g�V�(�T�́E�O�45DU��SPT�RP���tcZ0��sTe�Y��F�D��{O4H4\r[��	T�^T�O�ZdU�M�)S�dU)D�/H14����3el2�aR��_��aH���)��5蔵T�_6&\\�*J#'U6H�K_�I�|�9^�N��E�b�[Z2�fL�arQf��\\�K:V)ì7�ש6+*G�+�R��z-��h��f�j-�>@�o��Ire4�1kj�k��Z�I�_Qt�lK\0���Bv}v\$�0�mƔ�@����mE�7n�l?�n\0��f�io*Qo�6tKp@�p��ab\na��T��u8�J�.���>��b4�thot��J✵1uGD���n��Q5�mV?G �t�iOP�vԝw�?4pJ�{uwd��e7�r�;sOG�w1fz)�y�-nVZ2�Ew���1qS^F�|I�&%&��f0���>��@�tF�:k�RL�W�@K�~�j�э}��>7�~���@��F�p�@�u�|B�1�m��kc�_w7�H5&�_�_4��xVZ�׋p8GW��\0A���^�,\\�CgvF�}B�/T�'��8=o��	��e�ؽ�`���R�q�Yf)�X���	�\rBaȴ�rP��z�\"�`／ļT�yL���{�Ad�)+\$d�U�a�='�pq���W�a00����J��4�ǎT��܏��2vͅ�jl\r�Vޠ�~6\n�&@3��5��W\"x΄\r��P����\n���p�*��� q�Z4�x:(9��%Mt��\$X�W*3gu�,�d�@	���\0�4#ú)�<:�P�QS{�V_�ˊ�q�'��M4�ESX�S�=31��xv]�o�Ybx	���GZ�\$B\0��6nh\"UD���i܉x����.�H��87a?�t����9-�(�h'eD)��<-H=��F��՚=�6<�!z��2����>#�~��\r���+�d�\"FF5]�#>V�W�e���@^��C�ݤ霍���<���XO�9Z����Z�{��	�:��E�=C�5/c,��@�m����\\ĔF'X6�aɦ{�(a<���imt<�i�Z�F4%��'�`/C:c����@a֋[z8}��|�Fz�Bd<�~�{2��@\r���AW�w�;PW�*�-�d�ħ����Gc:��E���	\0t	��@�\n`";
            break;
        case 'tr':$f = "%���(�o9�L\";\rln2NF�a��i<��B�S`z4��h�P�\"2B!B��u:`�E��hr��2r	��L�c�Ab'��\0(`1ƃQ��p9b�(��Bi=�R�*|4��&`(�a1\r�ɮ|�^��Zɮ�K0�f�K���\n!L����x7̦ȭ� 4������k����|�\"tit�3-�z7eL��lD�a6�3ڜ�I7��F�Ӻ�AE=���F�qH7P�u�M�����n7��Q#�j|aŘ�'=���sx0�3����=g3�hȎ'a\0�=;C�h6)�j2;I`҉��\0��A	�j�%H\\:\$���&��0@�A#H�� ��:����#�\0�4�B\n��(ޡ��S\n;I�Ɯ�����B��9Ãk�:�ê�!�0��X�B�7�\0P���{�G�xҲ�	;�4=	�� ���\$��σ�>����#\$9��p�!pc������:\rz���T#�9�`@!c@�2���D4���9�Ax^;Ձp�:�(�\\��zNү���I�|6�MjB3#Qx�4�!�^0���Ɖ��+4#��D�ym(\"Q�92�ڼ(�*�5��<O31����,�U�7BL�!PTL�\"X���M��-@�08+t�j#C��'����4�����PH�{D�m�n�v�N�֯#-���L�,�b�43%8���Ô)��fd ���&\r(�P&�����&�:X,��Pܽ+�@Ÿqx�9b��	G�h0�c[��Y�=B�-�x��Q�O\"���x];�纄\0�x��>ӫ�)b,���.#�cn���� �z��,�Js�r�b�p8�!n[�^=���'��l�7CwZ�>�2��ܯ8#�9�[�`6H Q�C�h��2s�i����9㦃_���0@3#���#�D3Q}�7e���k@�ab`W���b�X��z��5�c��l�	b�2����~쨈B8���Bie��h�L��3�qĆ�֒��\"f�N<��TʡU*���#j�[�^J]��8*��,,K+��l�B�YW�\r�)f@'��ÚJ=�����T�l/�3!�8�IMQ�-t.�]��)A�O�bEE�}QV+��@`bF')���jg!,'T*�R�uR��j��*�9+Un�Ӽ���9��P�LWJ�0!\$��\"t7\$En.stg��|-������H��0-���neٸl䘉��[���0\$����n[ �}�T����Bњ��슷&EL��A�p*�@܁�9u Ǿ/�W�H��H\n\"s,t�HH�\"��fj�ւ(�w��rPPc�bĿ�|'Ь�>&Ţh.B�:��,��I5ȹ����\"�h���A��^�8AXG���\$f	�XA�8er���wf���pΩ	a��@��S�G(�ӣŀ��F������(r\r�)���\nvH㍢Ės;��^�>��qŨ��2�x 	�n.�\$��Y%B(&p ��GI���(Ab�<�[D@�I�0{1�Dɍ����[1����=��^�x2�@'�0�k�^ST\\�w�[,��(�-\0��hHyFj�P�ң}D�! s�+`��Ru���o�����^K�p��x(���i.Y��>�T���D�Mi�k��T�\"O�j�vJ�����{^\$�ӄ��P�*Z�r���Ҙ0@3x]W����xE	��|�\\wc�'#s�G�.`�ȑ*����ɞS�!����.xk9l���E�bH�\r�o�-@�}�@a�(����	��*<Ǖ,�Z���rb\$���8r���z�L�����F��#�t����E�7[�zq��>�����~��mN�M �:���I��gۄ�n\$�\"q��\"W�I��.7���_����_m,����Âad0j+#��RH��MԎ��IKa`9��#��������q\"���}�d��l��;ۺ�^F��c�DC6u�M�\"_�o��ȡ��6N����r㠨�NJ����G����k�R�NҐY?��}��1%KW{�&��>\n�!���#N��\\�p��2y�Z��2�d��yM`m�0����弇�/�����w�X��ݡW�00���lI�	\n|��L�E?;����ti�z �0��\nL�:�}��ˆ��a�\0_�ߚ�	�1:�1����z��靻�����{��/����_k�EQ�����C�C6.x�Nqv�lDIQ+r4`�C�eI��Te܆\\_�(g��J��G&�ŕ�����Na����=I\"�w�w�r���&���y�H!���s�������Go�s�j�~xu�=�f��?�5���U�s��q`1Ƙ�n�I�bWh,L��\n !ȴ�\"�P\\G�m\0��Ƭ )/�r`�Ę����٢\"�IB�D�\0�)&�N��Z��,����p�؂�\"�0�mZ�PU���B;�&�G����Jo���p�|�ڀ��#��|Љ/�����N�E07��\"���b�΄�N��	��0�A�r�P��t_β�p��k��������������ͻ��0�|P����E�R��ńLQ\0�m��I(�2\"�D^�e5�A�D�H9�%��J�6'#~�\0�	����(�T�p����/`��.M��F��c�ޅ��M4���e�P�1��|���Q��.���.01�\n��1���#�Oof��JfN�;l�\\F�E�l7��ď��\r0�	/��G�� .71��J;�Y�Ũ!ɾ\"ȩ �2d�Le���\"D�'��E�����m�\0F#� �ubA\$��]\"����&`�&��tJ+&քX��A!/��1.�!(��������@�C��<��9�9)��N�*lZx�T?��ݲ��\$:m�C��2��y)���r�-��\nrl���(�Ni)M.����2�Q�;q��gKq���>���1�c1R��E�Ʋ^iP�3�@�H�c]*82&��~^5sL�B���G�5 �L&\"�0�_�C5.z_���o.뮓5��8���Fp�u�,�#6\$C^�CZd�\r�V��c�8�\n���Z����J��������I�4g���3�L��\r0��Ն^9�\r:�i�n,��D���!>L�:h�%6E�)*y�.'(&��~d��!l����#���âD�F{�Pa2�7(���H�,��x�V�1\"1k�kGH%6�r��H�@\"�\$:�ˀ\$D�\"�i3!H�7�P[��䌬�\0	��:G��I͓�\0��c�>iƖ�&���_`�8�L ����Z�P����x�P�pg1\0�s'�tE�H�HM:E�`�pg�΢��\"d9%G�-��ƊF������&fc��";
            break;
        case 'uk':$f = "%���)��h-ZƂ���h.���� h-��m��h���Ć& h�#˘����.�(�.<�h�#�v���_�Ps94R\\����h�%��p�	Nm������c�L��4�PҒ�\0(`1ƃQ��p9�\$����&;d�H��5�}Q��\$���C��˩�Z��B��	D�8����(i�yA~�Gt(�y�g��Y�1~�Қ(��Bd��ׯK��m�JI���\r.(���V��V1>�#��\$:-���r�%C���Ǵ)/����t�p�^�\r���>�[73�'���6�SP�5dZ��{�h>/Ѡ���z0�)28�?��v�(P|\"��o���KB�\"i{*���� �5ϲ�:㹉��в���H���8ޣ�\"JB��Z�薉�(F�)��Z��Y(���\$�&�Y����6,�X\\�N�z�#����D�Z�9����)�ĵ+�;D�Lh1(�3�� �(1@ݷ��lhQ�ɠ�MH��>K� X Ě��!���аq�Q&���1�d3W�H�\\C�%�P�nTx�H��\$�D-���h��U͋^5��O�R�\"���\"9#:���h�ƁGQ8�mn#��N��O���*�# �4��(�&��Ԥ�!r��ް��X_ܥ�0�\\k�Usɐ��;(�~�����\r��3��:����x�\r��`Ap�9�x�7��9�c�v2��Fn�=,��@�Mn;GB���^0��jXƐ�/Q�� �Ѭ+�\rbe^V�8<\n��v�xZnz�	\n�O[D�_q��N��l��i��R�I�!7`PJ2biZB��ʫ\rs��5����hZ��~�H(5h���|�\$�`K ąD�U��D�ΐ���':�)Y:�%�<N����3u�^��j\"���Se#Y�j�/��p�f�<���Ǒr\r�AS\0Zޖ�|�)z���P��8���FF�V�T��z\$���,bdÛ��V.5'E�J,�4���V�@PS\n!1�1�hA��>&I��F_���:�Н��n��B\\�1%�����!D���BI��U�ġ�35b<NX���+x[*S]�H�aM�2�[\n�׍*o��\$+\$-h�����j��\"(���(k��@��b\r��?�v���rC���?a�7��\0��ZeE��:'�v�I�Pb����uVc�\$�Iѷ��+EQ�&?�����'�DA��0�i?~\n}8�㺨�rd���te�Z��\$hJ�Oe[���d���-,�lLI�xX˘��\n��ӎa&��F�9Й&Fe�g;3ɩ솳Nj�YT�&���6Yy�-�\$�s�_��0�qƞ	�y�73IDC��RI�\nG���	�=���8�*�7L��xL�Q6ɜh��x����aG(�&���rL�vt �71	N�,I�2FL�S,e���3FmP�;g���I*C��h\0��c\0��;Zk�yb/�c	M��2\$Q1�ڇ0�bR��XSEztIbQ*\n�0�e\rJV1N+��h�a��:�`�Tt�J��X��fʓX2,��Md�����Z�ً3�՛�t��@�BIV&��Ɓ�!T�觔DE�j�uWK��M)�N���D�>O�<��,9v�R[��:����q,�ҫwGdj\$�#�%�moFo��MCg���y����PPɥ�j�Ъ���Bm��-;Rl�Ųr=gEP���G+�eKt��*}\\@� 	�IH\n\0�52�	� h܂y}&¯a(h��#\n ��o\0Ӥ_L^�A\\�m= ;�@'��)��s�<]r1,Jh��y��	�邆��9��@�_�/H0���cث4����J�2v��iKɩI�H��6�0I-sC��.�\0��R�)� �z[;��D\"U\nJ�l!SZٺ���Fii�C&��5gH�&�.@�1\\ui��V�:�.��tӨH��S(���p����`�IZ+¾4\\�Э�\rӟ4 ����4&\$4�����\\\"��<��&J�nN���Ğ-)���T�^j<^\0�T~q]tN�g�6>7�[̅��=+6#��L�ԲG�k\\���6�je�*�;��� ���D����9���<���\$�wwO��Re\n�`�BI����F\n�B�h���#��_٘��B�F��\n�T��r.52\n�[:dۘ2|}KpӉ�0uI�_!���0^��M�do�ͻWە�Ʈ����|9e\\�r/�o)��A�p��%���� QV�n�Z���ϑ�%�Qn�D]�BM���X�CY1Yꤼ�����%<�@8�^�:�66(�N[���{�t�ɗ�M�t0�X�:h}w��J�Th���OZ\\e;bG�p��F�z��>k�\$�\n`]}��4��a`�r;�^k�=P|�b�a\"�\nl\n�P��FÆFm>0O@�l6�P��yMʏ6+�`mO�8x84�+��I�|�'�A�0o�f�h�4�d���'�*��n�*��d�2�O��NJD�B �Lج6�f����\$;������#(�Ŵ`���\rb\nfL�	p�\n�\\�G�T�6�\$�^�d&��*((�K��-D�e�D�Y��B�Pl��j�6�i���rlơ�F5�9�(���\$�l�����C�pWG�[#\"U\0�\n�� �	��'n��J�d����,�vj诌\0m-�t�,s\0^-��m��'�tt#u%5�ałA�9/b����I�,C�\0	�C�1&7���ьo�Y����!���,�qpS*X���@��uq������q1�<&%�R�\rw���l�cv%�;�����ܿ0�;�� ���� 1��q���\r�M 	�;�\"��ˏAhʀn�����3i���H��Ne�4�%nb��L�Z̴<!j=��T��{bPRx�0(#�li(Šl���b�^©'��a�<�\nz΅n0�[��}m\"\\o��D��Eq,1XHZH�G�*d�&�j��t�\$[/&^�l�/^�/b���Q�Qs\n'��o�0e���4*D�þ�k�ܓ+0��1?3h=�71��1�~<\$��SK3���d��:j�Xˮ�4��pC���(m�S�yI��'���D��z�ꮄ�\0�\n+C�/��OD/c���DV�.�7E�#S�*�2L��h�'pr��� Sb5�A5Q�!��v}\"�=�\"Gb,�d`F�rU�-����3��E*\r24\n�4�F36L^�æ8\n�Q��T̉�AG��O~O�C�!C�]D�2F�舱t(�T�5E�1�B��mE���ToED=ty0�M+�\\3�4���K����hL� qSH�2��zASgIn]I���(g�JGE\"+GW5sGKT�H�K�R�T�Jʣ6�;JѡKJA��\n����44G��P+��\r_P�!P��F��Q��QΣP�TM�GR�E�<�r����@�V�Ez'�� �d����[�]�h@UC�T�~;L0q?j�&��T���w7J_WP��]��uG+��P��=�鐛PU1\n.����j�sv����	�5%�.��?#H�NoB{RT�R��P�4S�9���iMU8Z�S�;\"P�^�Tl�3�c0�_��V!I.^�b�\n�	DV\rQU���b�a\"��_P��RE�2�^GP1\$>o#�+곑a(�8ǅB��]��N�xn5�Fk�b��I��7��HV�KV��ub6Gu�]k+���p����i�\rL��GO�B�	k�el/�a5l����Vv\\�D^T��-G\r�ȕa�b5F�7/�?���v?mTI\$��\0�[wO��O��VIqW*�լ���e3�s�tE��s�(�\"��sG_u�7d�sV�&u]th�\rjP�\n��d��\0��~\0�@�\0���%�\n��w�x��\0�yס��\r�x��p��7�_��`p�x�a�*T�_�'v�Z�E��W�7gr)v��\r.:Zumh��F�Za�QD�6,VGR�-l��/u�eR�#��yd�p�'A�2Z�����krAJ?\$��[v�N���c��%�4�6�FԷ2�Kg����L/�)��s���� ���1�+�1���\$�\$�n��8'd�%0[ �0�N�!�m��gI˳I�1�5�t�J�S���H7�\"��1jS�����3�ߏ��|���n�8)u.)�&yg(�K���]\$:�pP\"��j�o�ϮYQ�9�,\n���Z���Gau`�0���E�7W-�\\����QK1�T�k^�&ۗQ�Ь�\"	�/b{R�p5�lVF��*^cx#E4D\$ER���H�w]����U�8�:�Y'�\$*��8��y�&�d��̒��b\$g��?.��@&����L.q>G�v�5��3�Mp�\0�G�i5�{���q�ŉ������T,a��wv9�V��q�4�>|3V�(#k��oi��y��v.!Pm΅5M'����]%/8\0n޵`]�J�\no�ې#a-��n����-��X@� ���\r���Mհ�i�G8|���T!��7,�6rO/\$�	��jܼB.jF�0�Е��E�����\r�V;�����Uh�l�)t�*�R�;�=H��/4QUV\\��";
            break;
        case 'vi':$f = "%���(�ha�\r�q���]��Ҍ�]��c\rTnA�j��hc,\"	�b5H�؉q��	Nd)	R!/5�!PäA&n���&��0��cA��n8��QE\r ��Y�\$�Ey�t9D0�Q�(���Vh<&b�-�[��no���\n�(�U`�+�~�da���H��8i��D��\\�Pn��p��u<�4��k{�C3��	2Rum�����]/�tUږ[�]�7;q�q�w�N(�a;m��{\rB\n'�ٻ��_���2�[aT�k7��)��o9HH����0�c+�7���67�� �8�8@�������@����� \\��j L�+@�ƻ�l7)vO�IvL���:�I�枧��fa�k��jc�]�/�P!\0��d�!�� K� P� k�<�M\0��\r��@��h4�A�N!c3�(�7\$�X�b,(����R�-�2j�]��2<�!iJ N��A1���[�(�R�f1B�\"���\r������A���Z8B<��&u=SI#qtI>�(��0��P�2\r�����<9�ph��#��n������\0@C�҇\r㭘B�%\n�\0x����3��:����x�{��\r�a?/����p_p�c���I�|6��3?k�4���^0���2�T�.́BED�\"�,�9e���9)��:�&Y^�\"����;�\n��7ZH(J2/C��2��S)�c�s2�R̩���J�VJ\"!7���\"]q�����:��V6�qJć�eJZ7k,2 J���GV\n���5�����Hƈ3��Q7tW�céVI�~;U��6�Ê�4J4���Yf��B�Γ�F�\n#��TĮ�@�-џR��|[46'�h�(��P�Ccn�\r������)��+�u�ߢ�\$��\n�)C�4{�au�!�FTz~�ص%�W��h�UH�*]R�T���s�/��5��u��x� \"�l�b'@����:���\rP���r�D���Z�Hhy%����\r�3*�I�h;�����\\F� ����BnE�P.��)���]�,\\� Q��z^Q�fi��.�Hn2����T���	�3Õx�3���h�u~�a�r\\����Ȱ�8 ]+�v���ת���}ǐ\\������v��nbd-�1�B�&��~KQ�AC��:E4��ċY3o�Č�Eғ�K��TuK�\$�t.�ػ���^��|/��}dxr_��A��	`�Þ��(I,�\r�t=�\$�O3�Ie9��,�\"�o\"��] ��	�@K4:O�@��`l/H8X�C+D!���Ő���f�6��R�\$�	P�ĺ��n���R�����C)uN�^���K+���n�B�H\n�����%�IH\$���,��c���:> ��Iق!�����rƆ�P�{��:g\r#s�Ð15hAg��F�pnyp!&�����+3��\\7@\$�-�\0D�-�� �Y%tU�j�ZŠĚ��\"B�:×�bʫ�N���,�����Fd��P�h&t9�hS�\"v��dM}N!��tB�K՘��l:��BHy��?U��!�+�ӠT��Ҏ���*�i�BP�V>FSl�&�|�\0�¤L;��]���%zN8���bLC��,%�n�Sb[i�A�d���:�P�	I)h���J\"�)\$[�2��˸F\n���Y9rF�s�jiN\"��{�	�Bͺ\$߲�\nYmg1/\n3E�%�N�I�r/��#�<��t�BIT���Ds���aPb5^��t�\n�J����;��B�s������Y�%,�̢�Bed�s��=M*Y��`�(\n,�3&�}�	�hJ�	4(���fP��PBӦ���B2�\r2���s�E9�pzM�^ҀPV/�>����7:H�͙�u�]��%�bO�K3}��ϑ�ڡ�|�J��;ol���#6�P�كHz��d2�s�\r�fxy�%B \"ia�ef���}�q�V`(�*3HT�^��o��|�ԟ c�	MunV�x�\n�����tiLK�|&j����3�j�Ri�	�֙ML��ݩh#��FU�D�\n��iC�n�j�W�P*�\"f�X���,�W��a�Ia�����/��ڑt�\0́�Lv�\\[�:�����bPJ�ywM�E��B�yh���!Y9�ood`���ҿ�7b\$� ����B�&+����M�iX(\$��r\n�1!�%��^�f���E��[�Qh�s�� �\r��G[�3qe^P;�B7o~�~\n_���m_-��)eܠ���������%�r��	��c(����_��IsF�Q �2I�r�:~��ª6��BE.{~�;�B�/���z�\0B�f�!F��3���m�LfSo*�89��2�2�p6�K��PN-ڀ�>�6'@~�BB�t^\"�b-�@qb���6�D5�n��U�<FAڡv�\\8�|N8�\\6����\"\0k�2˧KD��В����d�\0�|�m\0�Ă(���@��O\0�H{p���S`�(�̀g�{��!��n�/m����Ĳ�*�c�[ń\\����'&����1hv<�����\"B �*��m	0�ƣ	n���0��C<B�f���9�|p�bqN�/�Q�v��Ed:��o\n\r����QnG�rŦ<�+�\rc�\$�{B0(�4H�8��P����{D��Ѣi��f&gYP�e�L�	f°�0� ��N��q[�!����W��Ѭj��Gg�'�9x0���H�m\".~�F��,dl<��aN7\\8\$J���R!v�̜�nR:T\\��~���� ��r��F.tXR�o'��{'��z���������(�3\"ڐ�Sp>�p6\"��5HJ�,�pA�����L�\rO�Z��-H�)2�I��*'�2�-Rk�~M��(e˕��.R\$\"%0� ֐D\0�f=PP��*s�11'�@���l���2s2�0+�*h�\"&�Xg��2���B�-\r�5��5���dѓh��n�s	7b�y�WM�f��0�15n��Q�`e���9o6��Q3��3����,\0��}&��FJ#cN~#V0͠���W�������*�o�H#��q&:�d�eX'O�s���\nV�(��	�8��bРLZp�J�d�12k�%-\$x��Z\"�;k�/pf4��R\n���Z��kD���Ln�1�TqL��B\"G�r�e0n}��W���-2����H&�-\rr�Br�C6&�hmF8j��0F@T�x6B�U�-����v�pˎQ\$���0,\r#��FA/:p\0,�QZF��v`��bZ�t�Q�N��&.6��/�Ŧ_�o�\$84tT��P�L�Jli�27j�<�E,�K�S��T\"V%�\n-�?�\$35:�\$��q%?�\"��ro�|�	dֱ@�0�kӾj��Pa^0���J]%\"��g�U0��*ƥ<�u`4I��u�)fK3�m��@����h�.���B)m�f��C:W�Wl�mƺ��4B�";
            break;
        case 'zh':$f = "%��:�\$\nr.����r/d�Ȼ[8� S�8�r�NT*Ю\\9�HH�Z1!S�V�J�@%9��Q�l]m	F�U��*qQ;C��f4����u�s�U�Ut� �w��:�t\nr���U:.:�PǑ.�\r7d^%��u��)c�x�U`�F��j���rs'Pn��A̛ZE��f��]��E�v���it�U��λS�ծ{�����P��g5�	E�P�N�1	V�\n���W�]\n�!z�s���ΟR�R������V�I:�(�s#.UzΠ@�:w'_�T\$��pV�L��D�')bJ�\$�pŢ���[�MZ��\n.����>s��K��AZK��aL��HAtF3���D�!zH����C��*r�e��^�K#�s��X�g)<��v׬h�E')2���Anr�j����\n:�1'+ֲ2izJ���sͲ� ��h�7���]�	9H���N_�es���K��?	RY4=D��F�@4C(��C@�:�t��T3��>��x�3��(���9�����I�|��1B�:LΝ\$=0�!�t�I�E�'5(����RMy&s�#SE�͒CH���]K�:KC%�um0�K�V��)\"EA(�CGA�Fpܗ&���fTY��C�G)\0D��G�S�W�)\0^c���T�e��wa D#��8s��*.]��\"h^��9zW#�s\0]c����9�a D�j<V���]2��g��C�\$�CC�m�8)�\"e��ntI����4�}�|=3Q'�'1Q,���g^����n�K�s��SL̋&��e�Ȝ�:Ͽ���C3�4�;���.Ml,�c���a�KLN�uDA-�uPDJ�O66����ҙt_����N�o-6M��HX��:V.,38C�\$�.���fC\$��H�jW����D��ʗLYO��D����=O���+Ÿ�t	�\"MRDb�L��:��\n�T��T��Z���V���@��t\r0}`�e�`�,\" t�bKGH���N�\"�\"A��ZE܈���i���C��[�c4��yH!)52�&,������<��TʡUuX���Vj�[�P��s�j�`�5�\0:	Ap�h�B(�E��\$�\$W1�,��\"r��,�E���)4H:�a��>�Ŋ�H9���kd�vg�ń��?�M���#����\$#�ȁ�9t�,Ǩ����z�Z��) @@Pv����*!Y8��(�BLJ���8R�1`9�Ky2C�U���#��0�hDJ��bʹ{m�Rʘ� NAr@���a�(��3�|W��T&&��LZ(��(�ĩ-I\0PN#\n\"\0C\naH#.)&ĳ#g�#/I�\$V�&.��F��8:����E3�9)%hhs\n�j��;?c���a@�ˉ\\�p� Z�琻��W�*?HO��\"\n\"Af��\\��0����TuJP��QH)��\0r�<�	\n������ES�`:D+=g힋΄1������`�y�'��	�/5�\\G%f,�R��rS�܊A,x��K\"�\\����\0U\n �@�i�@D�0\"��L%騵L	�o�%�n���S2Qm)�8R�ޘ	�B�� �����Y8�9������P�39�fK�Cִ�ټ���8�s����E���7��,�(���&��i�6e�E��ژB[��P��]I89��nf�蠴v����fs�p��\r���\"NAʼg:M(����v�u�Ӟ)�bA9�ɚ\n&ڜ\r\"�8���t\$ �QB�C��:-�mu\$6|J4\0�(c\rxT!��BA\0c�k��\$R�۽��d�lЖ�@�EpM�&/����O��&�����!�0�I�p�#���#d�r��P �0�9��F/L��|��`,�\rF%�Ӣ�XK\"�#3�R�T\n7ĺC#�L�Iu_H�M�D�lR5��.:�'��۫v;�غ���{�a��.@D@�m�}bH��c���'E+VY,f��(bݖ`TYm������GC�\rB���5V�av!Z��\\���z-��E^B�^��]\"5^�snD�1E�f��5�rU�W4��\\��'�f��d��9�/�RzF�K@��`G1�\0��\$�G5��b�I�7��W.\":�s�\$�!Nl�r'By�����g���&\n,:�:�wz������t|�5��j/�~�=.�c��9ۙ^����|`�?B�A����P��O�ʝ�f����Z�F��W���^�*���9<��\$r�)Q�����DȲI��l���BT��ْz�(�c�d�C�/�,�F�3��:�!\"S���%w��M7H_�Y�A�rX�s~<�WK����Ʃ���/�w����Ĺ�p�Ȟ��M#/��Т|�%�B0�P]0k�P&�\"@-��e������`�|�.�CJ�*�P���(M��2�E�0h��a[-�Hy!rr�T��m�OFV�g��8��6ϐ-�Z��a0r�0�\0�W\n�w�����p�nd9��0��\np�^����\re6W@@@�V�@��\r��\0��\\P��P�u0�@�w��1��\r� ��,��@����,�\rn�\0Lڣ����l���P��DoZ�И����\0DdK�\r\r�L�q{\rQ�.��WL��/F��l2B�A>�mh3�b�M�5��:(\$h-\nzB{�7\$�i���p��Fק��P�gz\r�v��~�p� 3�F)d�� �O��O��C�\n���Zh���@���C��!�Nd��%�����H.!�.6ì\"�6�������dgr����T3����&�p�*���������\\J��.&��͂��Rn���B��ҭk�+n���ƒ�.�B��`��t�cB�&dǲx���+�����+.��K����DdZ���[Ah� �V\n��`��چ�,	\n�:r�<�\\���\"BLR�)����1�����43F��:j���s)�6���c�{��H��L";
            break;
        case 'zh-tw':$f = "%��:�\$\ns�.e�UȸE9PK72�(�P�h)ʅ@�:i	��a�Je �R)ܫ{��	Nd(�vQDCѮUjaʜTOAB�P�b2��a��r\nr/Tu�ʮM9R��z�?Tא��9>�S��Ne�I̜D�hw2Y2�P�c����мW���*�=s���7���B��9�J����\"X�Q��2��M�/�J2�@\"�W��r�TD�{u������t�s�p�������S��\\=\0�V����p��\"R� )ЪOH����ΔT\\ӊ�:}J�F+��JV�*r�EZ�s�!Z�y���V꽯yP��A.���yZ�6Y�I�)\ns	�Z����̢��[��2�̒�K�d�J���12A\$�&���Y+;ZY+\$j[GAn�%�J��s�t)�P��)<�?��\0U��w�*�x].�2����ft+<�Kd���(A2]��*�X!rB��\n# �4��(�t��E\r�l	�Tr��{:�OpbJBO�:�F�@4C(��C@�:�t�㽄4�)K�x�3��(���9��P��I�|t(�B�1֯�B�|�\$	q���/��9H]D�̸�et\\���K6���?஋LQ��\\��1�H�@PJ2�:�@��ea	&�s�2��S�o1Q�d��0�ם3M�e�w�d:<C��)xG�d�r�B�H���re��B�i�^��1I@\"Z���P@fg1pM�	j^�B��<��J��L�f*�8��3(ڰ:�s� �(��&^�)�D�r\n�淘1(\\մ	�_�ܾ��7�&]�>Tt7�34���ҥ�r<���/hC�H�Hs���,��o7|�u+�wx!1	EA�D�!?�'^w����G��\"�\n��(�:K�6t�e�M���Q�'qAo��'1*K��\\j�Dq0J�t �r���\"@��:D8�[��̈�^\"���wdB	��+�YKB�H�5*�#�B��l�@�6EX�5j��ʻW��`�5�	�B�Y�8�@��t\r1l��#WЬŔD��D���AL�\"WB�)AL2�8��R0���8\"jߊ�)D=B ��Ŕk�|��Uh��ºW��`, �4'Y+-f����ps��am@T<�(���=�,�h��L��5��+�ю2�(���^TQ\n����ȭ�|F��f!�9��ÖB\$\$9��6 �a\$P��~/�r���'Ź�3���0��\"&E@���.Һ�p��U�\0��Λ�nqH�N��sS�|��3Z(�ХB��s��\0i���~)��W3����\$̙���(�Uj�>��\$�\r��-#�P�^�\r�b�s	�^L�p��!R�����p�t*nP�+C�0 aL)`@xkY��KA<b�h�5����Q,+��K�\0�AF�(����� Mfh������Δ���a9��3HL\\\neN^&����9U�%�0�Q�t�q8�DH�Qtt��DR��O\naP*�H�ɉ=l�l��|��\"]�\"*��UV�9������ �\0�P(�\$,�c'p�0�9zq�ΘG#L���(F�R�(	3I�<'\0� A\n�]�@(L����1zPĀ�䌏��ZE�ͺ�����a�1�8�c�v�Ye`\\�B;�'�n��^�9��<!SǄ�b�^r��zm\0�<�dJ�q ��@�k����#N�H@��B�+\\\"DAw��M=SZ#����N�������(T�Va�J���dX\$�NG��3h���\n�q�<8\nSꀫ�u����S*�G��U0!L���s�ADВÛs��B�qy�n��B@�0�H&\\���U�R]�:n�\na�2�0����!�=I��0�F�LLl���NjD����O0�F�ΒƄ�/�2M��Qv-��wN�B4�!pb�@���@��@�>�r˘=�)w%Q=�|��TyX� ���1��G����������4H\\^��'���26\0H��v))Lכ&]�ơ@��Fw�&^��c��N�7lF/2\\�E������	IA��N<E�&:%��\"r\\\\�|t�H�NG�-Ǒ@ nBp�Zז��ķ�-#,�]n�| ��z���-�轺��?d�ӈ�D�.b�/a3a�A�A�Sјh���[#��E��5F-�L��sу\n��WO,��<��\rh:��4R��T�a�o����7�6�>#������Ȋ���h�7��d��/�����B<Z���s��F�ᡴB_L>��7b=�ý�x��b�]�u~�O���˞�b��}AH��/����9��\\�qu�E��#��~*�8�wΠ2�\0��.��\n����\$�k��K����g/m>���ڐ%���-&|�C���P5�,�bS#�n2I��JnO�d�^�/�����J�j>\n֤/���!�(�	�\$�[�N(��{�;�@��g,����c4��I���`cl���l������n�gxC����l�٦���p���+\r0�C��\r�\$.�t!:,��战\"���%�7��#v����p�_�*��[Lbk��r����2S^(�7�e��T7�Z�D�\"p^���<�fB�:�A|/-�P��z��o1%!I/�m�F�q�Ol.MFԠ�Ve�c �Y�@��\r��\0��\\�M_����(�'�R��q��Ѽq�@��F9E!��ձ���emf-�Nq�s\rs!�k��P��+\"Mv���#2>.�H\$��v�i�Kd�!�N0�SP�B2P��	�Ԁ�!B�b�̂4�tC�ޮ<i��ƕ�P��R��jD���c���az!ph!\n.:C�����C��AB�1�\n�2���g�\r��-��:��%�bg\$��O�E���V\n���Z�u���h�.:�J!bx�z[���M2^k���K)�9C��[��0l�P��E�4�2�H�>Jցí5��b�t*�F�Q���z,7�:t�j�o*A�*�\0�!`^/�ϸ�F��O@w�;��0�)VƘi�<��N�\"����!zfF�����N���Ap.a*��^��� ���\r��6���ρD��d�\r(�I�.�7�s8��*C	Jx4(���9R�<�L��g'�-'a\$�\"<)���,BHO��";
            break;
    }$si = [];
    foreach (explode("\n", lzw_decompress($f)) as $X) {
        $si[] = (strpos($X, "\t") ? explode("\t", $X) : $X);
    }

return $si;
}if (! $si) {
    $si = get_translations($ca);
    $_SESSION['translations'] = $si;
}if (extension_loaded('pdo')) {
    abstract class PdoDb
    {
        public $server_info;

        public $affected_rowsvar;

        public $errnovar;

        public $errorvar;

        protected $pdo;

        private $result;

        public function dsn($hc, $V, $F, $yf = [])
        {
            $yf[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_SILENT;
            $yf[\PDO::ATTR_STATEMENT_CLASS] = ['Adminer\PdoDbStatement'];
            try {
                $this->pdo = new \PDO($hc, $V, $F, $yf);
            } catch (Exception$Cc) {
                auth_error(h($Cc->getMessage()));
            }$this->server_info = @$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
        }

        abstract public function select_db($Kb);

        public function quote($Q)
        {
            return $this->pdo->quote($Q);
        }

        public function query($H, $Bi = false)
        {
            $I = $this->pdo->query($H);
            $this->error = '';
            if (! $I) {
                [, $this->errno, $this->error] = $this->pdo->errorInfo();
                if (! $this->error) {
                    $this->error = lang(21);
                }

return false;
            }$this->store_result($I);

            return $I;
        }

        public function multi_query($H)
        {
            return $this->result = $this->query($H);
        }

        public function store_result($I = null)
        {
            if (! $I) {
                $I = $this->result;
                if (! $I) {
                    return false;
                }
            }if ($I->columnCount()) {
                $I->num_rows = $I->rowCount();

                return $I;
            }$this->affected_rows = $I->rowCount();

            return true;
        }

        public function next_result()
        {
            if (! $this->result) {
                return false;
            }$this->result->_offset = 0;

            return @$this->result->nextRowset();
        }

        public function result($H, $o = 0)
        {
            $I = $this->query($H);
            if (! $I) {
                return false;
            }$K = $I->fetch();

            return $K[$o];
        }
    }class PdoDbStatement extends \PDOStatement
    {
        public $_offset = 0;

        public $num_rowsvar;

        public function fetch_assoc()
        {
            return $this->fetch(\PDO::FETCH_ASSOC);
        }

        public function fetch_row()
        {
            return $this->fetch(\PDO::FETCH_NUM);
        }

        public function fetch_field()
        {
            $K = (object) $this->getColumnMeta($this->_offset++);
            $K->orgtable = $K->table;
            $K->orgname = $K->name;
            $K->charsetnr = (in_array('blob', (array) $K->flags) ? 63 : 0);

            return $K;
        }

        public function seek($D)
        {
            for ($u = 0; $u < $D; $u++) {
                $this->fetch();
            }
        }
    }
}$bc = [];
function add_driver($v, $C)
{
    global $bc;
    $bc[$v] = $C;
}function get_driver($v)
{
    global $bc;

    return $bc[$v];
}abstract class SqlDriver
{
    public static $ig = [];

    public static $de;

    protected $conn;

    protected $types = [];

    public $editFunctions = [];

    public $unsigned = [];

    public $operators = [];

    public $functions = [];

    public $grouping = [];

    public $onActions = 'RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT';

    public $inout = 'IN|OUT|INOUT';

    public $enumLength = "'(?:''|[^'\\\\]|\\\\.)*'";

    public $generated = [];

    public function __construct($g)
    {
        $this->conn = $g;
    }

    public function types()
    {
        return call_user_func_array('array_merge', array_values($this->types));
    }

    public function structuredTypes()
    {
        return array_map('array_keys', $this->types);
    }

    public function enumLength($o) {}

    public function unconvertFunction($o) {}

    public function select($R, $M, $Z, $pd, $_f = [], $_ = 1, $E = 0, $ng = false)
    {
        global $b;
        $Yd = (count($pd) < count($M));
        $H = $b->selectQueryBuild($M, $Z, $pd, $_f, $_, $E);
        if (! $H) {
            $H = 'SELECT'.limit(($_GET['page'] != 'last' && $_ != '' && $pd && $Yd && JUSH == 'sql' ? 'SQL_CALC_FOUND_ROWS ' : '').implode(', ', $M)."\nFROM ".table($R), ($Z ? "\nWHERE ".implode(' AND ', $Z) : '').($pd && $Yd ? "\nGROUP BY ".implode(', ', $pd) : '').($_f ? "\nORDER BY ".implode(', ', $_f) : ''), ($_ != '' ? +$_ : null), ($E ? $_ * $E : 0), "\n");
        }$Ch = microtime(true);
        $J = $this->conn->query($H);
        if ($ng) {
            echo $b->selectQuery($H, $Ch, ! $J);
        }

return $J;
    }

    public function delete($R, $wg, $_ = 0)
    {
        $H = 'FROM '.table($R);

        return queries('DELETE'.($_ ? limit1($R, $H, $wg) : " $H$wg"));
    }

    public function update($R, $O, $wg, $_ = 0, $fh = "\n")
    {
        $Ui = [];
        foreach ($O as $z => $X) {
            $Ui[] = "$z = $X";
        }$H = table($R)." SET$fh".implode(",$fh", $Ui);

        return queries('UPDATE'.($_ ? limit1($R, $H, $wg, $fh) : " $H$wg"));
    }

    public function insert($R, $O)
    {
        return queries('INSERT INTO '.table($R).($O ? ' ('.implode(', ', array_keys($O)).")\nVALUES (".implode(', ', $O).')' : ' DEFAULT VALUES'));
    }

    public function insertUpdate($R, $L, $G)
    {
        return false;
    }

    public function begin()
    {
        return queries('BEGIN');
    }

    public function commit()
    {
        return queries('COMMIT');
    }

    public function rollback()
    {
        return queries('ROLLBACK');
    }

    public function slowQuery($H, $ei) {}

    public function convertSearch($w, $X, $o)
    {
        return $w;
    }

    public function convertOperator($uf)
    {
        return $uf;
    }

    public function value($X, $o)
    {
        return method_exists($this->conn, 'value') ? $this->conn->value($X, $o) : (is_resource($X) ? stream_get_contents($X) : $X);
    }

    public function quoteBinary($Tg)
    {
        return q($Tg);
    }

    public function warnings()
    {
        return '';
    }

    public function tableHelp($C, $be = false) {}

    public function hasCStyleEscapes()
    {
        return false;
    }

    public function supportsIndex($S)
    {
        return ! is_view($S);
    }

    public function checkConstraints($R)
    {
        return
        get_key_vals('SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = '.q($_GET['ns'] != '' ? $_GET['ns'] : DB).'
AND t.TABLE_NAME = '.q($R)."
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'");
    }
}$bc['sqlite'] = 'SQLite';
if (isset($_GET['sqlite'])) {
    define('Adminer\DRIVER', 'sqlite');
    if (class_exists('SQLite3')) {
        class SqliteDb
        {
            public $extension = 'SQLite3';

            public $server_infovar;

            public $affected_rowsvar;

            public $errnovar;

            public $errorvar;

            private $link;

            public function __construct($q)
            {
                $this->link = new \SQLite3($q);
                $Xi = $this->link->version();
                $this->server_info = $Xi['versionString'];
            }

            public function query($H)
            {
                $I = @$this->link->query($H);
                $this->error = '';
                if (! $I) {
                    $this->errno = $this->link->lastErrorCode();
                    $this->error = $this->link->lastErrorMsg();

                    return false;
                } elseif ($I->numColumns()) {
                    return new Result($I);
                }$this->affected_rows = $this->link->changes();

                return true;
            }

            public function quote($Q)
            {
                return is_utf8($Q) ? "'".$this->link->escapeString($Q)."'" : "x'".reset(unpack('H*', $Q))."'";
            }

            public function store_result()
            {
                return $this->result;
            }

            public function result($H, $o = 0)
            {
                $I = $this->query($H);
                if (! is_object($I)) {
                    return false;
                }$K = $I->fetch_row();

                return $K ? $K[$o] : false;
            }
        }class Result
        {
            public $num_rows;

            private $result;

            public $offsetprivate = 0;

            public function __construct($I)
            {
                $this->result = $I;
            }

            public function fetch_assoc()
            {
                return $this->result->fetchArray(SQLITE3_ASSOC);
            }

            public function fetch_row()
            {
                return $this->result->fetchArray(SQLITE3_NUM);
            }

            public function fetch_field()
            {
                $d = $this->offset++;
                $U = $this->result->columnType($d);

                return (object) ['name' => $this->result->columnName($d), 'type' => $U, 'charsetnr' => ($U == SQLITE3_BLOB ? 63 : 0)];
            }

            public function __destruct()
            {
                return $this->result->finalize();
            }
        }
    } elseif (extension_loaded('pdo_sqlite')) {
        class SqliteDb extends PdoDb
        {
            public $extension = 'PDO_SQLite';

            public function __construct($q)
            {
                $this->dsn(DRIVER.":$q", '', '');
            }

            public function select_db($k)
            {
                return false;
            }
        }
    }if (class_exists('Adminer\SqliteDb')) {
        class Db extends SqliteDb
        {
            public function __construct()
            {
                parent::__construct(':memory:');
                $this->query('PRAGMA foreign_keys = 1');
            }

            public function select_db($q)
            {
                if (is_readable($q) && $this->query('ATTACH '.$this->quote(preg_match('~(^[/\\\\]|:)~', $q) ? $q : dirname($_SERVER['SCRIPT_FILENAME'])."/$q").' AS a')) {
                    parent::__construct($q);
                    $this->query('PRAGMA foreign_keys = 1');
                    $this->query('PRAGMA busy_timeout = 500');

                    return true;
                }

return false;
            }

            public function multi_query($H)
            {
                return $this->result = $this->query($H);
            }

            public function next_result()
            {
                return false;
            }
        }
    }class Driver extends SqlDriver
    {
        public static $ig = ['SQLite3', 'PDO_SQLite'];

        public static $de = 'sqlite';

        protected $types = [['integer' => 0, 'real' => 0, 'numeric' => 0, 'text' => 0, 'blob' => 0]];

        public $editFunctions = [[], ['integer|real|numeric' => '+/-', 'text' => '||']];

        public $operators = ['=', '<', '>', '<=', '>=', '!=', 'LIKE', 'LIKE %%', 'IN', 'IS NULL', 'NOT LIKE', 'NOT IN', 'IS NOT NULL', 'SQL'];

        public $functions = ['hex', 'length', 'lower', 'round', 'unixepoch', 'upper'];

        public $grouping = ['avg', 'count', 'count distinct', 'group_concat', 'max', 'min', 'sum'];

        public function __construct($g)
        {
            parent::__construct($g);
            if (min_version(3.31, 0, $g)) {
                $this->generated = ['STORED', 'VIRTUAL'];
            }
        }

        public function structuredTypes()
        {
            return array_keys($this->types[0]);
        }

        public function insertUpdate($R, $L, $G)
        {
            $Ui = [];
            foreach ($L as $O) {
                $Ui[] = '('.implode(', ', $O).')';
            }

return queries('REPLACE INTO '.table($R).' ('.implode(', ', array_keys(reset($L))).") VALUES\n".implode(",\n", $Ui));
        }

        public function tableHelp($C, $be = false)
        {
            if ($C == 'sqlite_sequence') {
                return 'fileformat2.html#seqtab';
            }if ($C == 'sqlite_master') {
                return "fileformat2.html#$C";
            }
        }

        public function checkConstraints($R)
        {
            preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~', $this->conn->result("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R)), $De);

            return array_combine($De[2], $De[2]);
        }
    }function idf_escape($w)
    {
        return '"'.str_replace('"', '""', $w).'"';
    }function table($w)
    {
        return idf_escape($w);
    }function connect($Cb)
    {
        [, , $F] = $Cb;
        if ($F != '') {
            return lang(22);
        }

return new Db;
    }function get_databases()
    {
        return [];
    }function limit($H, $Z, $_, $D = 0, $fh = ' ')
    {
        return " $H$Z".($_ !== null ? $fh."LIMIT $_".($D ? " OFFSET $D" : '') : '');
    }function limit1($R, $H, $Z, $fh = "\n")
    {
        return preg_match('~^INTO~', $H) || get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')") ? limit($H, $Z, 1, 0, $fh) : " $H WHERE rowid = (SELECT rowid FROM ".table($R).$Z.$fh.'LIMIT 1)';
    }function db_collation($k, $ib)
    {
        return get_val('PRAGMA encoding');
    }function engines()
    {
        return [];
    }function logged_user()
    {
        return get_current_user();
    }function tables_list()
    {
        return get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name");
    }function count_tables($j)
    {
        return [];
    }function table_status($C = '')
    {
        $J = [];
        foreach (get_rows("SELECT name AS Name, type AS Engine, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') ".($C != '' ? 'AND name = '.q($C) : 'ORDER BY name')) as $K) {
            $K['Rows'] = get_val('SELECT COUNT(*) FROM '.idf_escape($K['Name']));
            $J[$K['Name']] = $K;
        }foreach (get_rows('SELECT * FROM sqlite_sequence', null, '') as $K) {
            $J[$K['name']]['Auto_increment'] = $K['seq'];
        }

return $C != '' ? $J[$C] : $J;
    }function is_view($S)
    {
        return $S['Engine'] == 'view';
    }function fk_support($S)
    {
        return ! get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");
    }function fields($R)
    {
        $J = [];
        $G = '';
        foreach (get_rows('PRAGMA table_'.(min_version(3.31) ? 'x' : '').'info('.table($R).')') as $K) {
            $C = $K['name'];
            $U = strtolower($K['type']);
            $l = $K['dflt_value'];
            $J[$C] = ['field' => $C, 'type' => (preg_match('~int~i', $U) ? 'integer' : (preg_match('~char|clob|text~i', $U) ? 'text' : (preg_match('~blob~i', $U) ? 'blob' : (preg_match('~real|floa|doub~i', $U) ? 'real' : 'numeric')))), 'full_type' => $U, 'default' => (preg_match("~^'(.*)'$~", $l, $B) ? str_replace("''", "'", $B[1]) : ($l == 'NULL' ? null : $l)), 'null' => ! $K['notnull'], 'privileges' => ['select' => 1, 'insert' => 1, 'update' => 1, 'where' => 1, 'order' => 1], 'primary' => $K['pk']];
            if ($K['pk']) {
                if ($G != '') {
                    $J[$G]['auto_increment'] = false;
                } elseif (preg_match('~^integer$~i', $U)) {
                    $J[$C]['auto_increment'] = true;
                }$G = $C;
            }
        }$xh = get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R));
        $w = '(("[^"]*+")+|[a-z0-9_]+)';
        preg_match_all('~'.$w.'\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i', $xh, $De, PREG_SET_ORDER);
        foreach ($De as $B) {
            $C = str_replace('""', '"', preg_replace('~^"|"$~', '', $B[1]));
            if ($J[$C]) {
                $J[$C]['collation'] = trim($B[3], "'");
            }
        }preg_match_all('~'.$w.'\s.*GENERATED ALWAYS AS \((.+)\) (STORED|VIRTUAL)~i', $xh, $De, PREG_SET_ORDER);
        foreach ($De as $B) {
            $C = str_replace('""', '"', preg_replace('~^"|"$~', '', $B[1]));
            $J[$C]['default'] = $B[3];
            $J[$C]['generated'] = strtoupper($B[4]);
        }

return $J;
    }function indexes($R, $h = null)
    {
        global $g;
        if (! is_object($h)) {
            $h = $g;
        }$J = [];
        $xh = $h->result("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R));
        if (preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i', $xh, $B)) {
            $J[''] = ['type' => 'PRIMARY', 'columns' => [], 'lengths' => [], 'descs' => []];
            preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i', $B[1], $De, PREG_SET_ORDER);
            foreach ($De as $B) {
                $J['']['columns'][] = idf_unescape($B[2]).$B[4];
                $J['']['descs'][] = (preg_match('~DESC~i', $B[5]) ? '1' : null);
            }
        }if (! $J) {
            foreach (fields($R) as $C => $o) {
                if ($o['primary']) {
                    $J[''] = ['type' => 'PRIMARY', 'columns' => [$C], 'lengths' => [], 'descs' => [null]];
                }
            }
        }$Ah = get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($R), $h);
        foreach (get_rows('PRAGMA index_list('.table($R).')', $h) as $K) {
            $C = $K['name'];
            $x = ['type' => ($K['unique'] ? 'UNIQUE' : 'INDEX')];
            $x['lengths'] = [];
            $x['descs'] = [];
            foreach (get_rows('PRAGMA index_info('.idf_escape($C).')', $h) as $Sg) {
                $x['columns'][] = $Sg['name'];
                $x['descs'][] = null;
            }if (preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($C).' ON '.idf_escape($R), '~').' \((.*)\)$~i', $Ah[$C], $Fg)) {
                preg_match_all('/("[^"]*+")+( DESC)?/', $Fg[2], $De);
                foreach ($De[2] as $z => $X) {
                    if ($X) {
                        $x['descs'][$z] = '1';
                    }
                }
            }if (! $J[''] || $x['type'] != 'UNIQUE' || $x['columns'] != $J['']['columns'] || $x['descs'] != $J['']['descs'] || ! preg_match('~^sqlite_~', $C)) {
                $J[$C] = $x;
            }
        }

return $J;
    }function foreign_keys($R)
    {
        $J = [];
        foreach (get_rows('PRAGMA foreign_key_list('.table($R).')') as $K) {
            $r = &$J[$K['id']];
            if (! $r) {
                $r = $K;
            }$r['source'][] = $K['from'];
            $r['target'][] = $K['to'];
        }

return $J;
    }function view($C)
    {
        return ['select' => preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU', '', get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($C)))];
    }function collations()
    {
        return isset($_GET['create']) ? get_vals('PRAGMA collation_list', 1) : [];
    }function information_schema($k)
    {
        return false;
    }function error()
    {
        global $g;

        return h($g->error);
    }function check_sqlite_name($C)
    {
        global $g;
        $Kc = 'db|sdb|sqlite';
        if (! preg_match("~^[^\\0]*\\.($Kc)\$~", $C)) {
            $g->error = lang(23, str_replace('|', ', ', $Kc));

            return false;
        }

return true;
    }function create_database($k, $hb)
    {
        global $g;
        if (file_exists($k)) {
            $g->error = lang(24);

            return false;
        }if (! check_sqlite_name($k)) {
            return false;
        }try {
            $A = new SqliteDb($k);
        } catch (Exception$Cc) {
            $g->error = $Cc->getMessage();

            return false;
        }$A->query('PRAGMA encoding = "UTF-8"');
        $A->query('CREATE TABLE adminer (i)');
        $A->query('DROP TABLE adminer');

        return true;
    }function drop_databases($j)
    {
        global $g;
        $g->__construct(':memory:');
        foreach ($j as $k) {
            if (! @unlink($k)) {
                $g->error = lang(24);

                return false;
            }
        }

return true;
    }function rename_database($C, $hb)
    {
        global $g;
        if (! check_sqlite_name($C)) {
            return false;
        }$g->__construct(':memory:');
        $g->error = lang(24);

        return @rename(DB, $C);
    }function auto_increment()
    {
        return ' PRIMARY KEY AUTOINCREMENT';
    }function alter_table($R, $C, $p, $cd, $nb, $sc, $hb, $Ba, $Wf)
    {
        global $g;
        $Ni = ($R == '' || $cd);
        foreach ($p as $o) {
            if ($o[0] != '' || ! $o[1] || $o[2]) {
                $Ni = true;
                break;
            }
        }$c = [];
        $Kf = [];
        foreach ($p as $o) {
            if ($o[1]) {
                $c[] = ($Ni ? $o[1] : 'ADD '.implode($o[1]));
                if ($o[0] != '') {
                    $Kf[$o[0]] = $o[1][0];
                }
            }
        }if (! $Ni) {
            foreach ($c as $X) {
                if (! queries('ALTER TABLE '.table($R)." $X")) {
                    return false;
                }
            }if ($R != $C && ! queries('ALTER TABLE '.table($R).' RENAME TO '.table($C))) {
                return false;
            }
        } elseif (! recreate_table($R, $C, $c, $Kf, $cd, $Ba)) {
            return false;
        }if ($Ba) {
            queries('BEGIN');
            queries("UPDATE sqlite_sequence SET seq = $Ba WHERE name = ".q($C));
            if (! $g->affected_rows) {
                queries('INSERT INTO sqlite_sequence (name, seq) VALUES ('.q($C).", $Ba)");
            }queries('COMMIT');
        }

return true;
    }function recreate_table($R, $C, $p, $Kf, $cd, $Ba = 0, $y = [], $dc = '', $na = '')
    {
        global $m;
        if ($R != '') {
            if (! $p) {
                foreach (fields($R) as $z => $o) {
                    if ($y) {
                        $o['auto_increment'] = 0;
                    }$p[] = process_field($o, $o);
                    $Kf[$z] = idf_escape($z);
                }
            }$mg = false;
            foreach ($p as $o) {
                if ($o[6]) {
                    $mg = true;
                }
            }$fc = [];
            foreach ($y as $z => $X) {
                if ($X[2] == 'DROP') {
                    $fc[$X[1]] = true;
                    unset($y[$z]);
                }
            }foreach (indexes($R) as $fe => $x) {
                $e = [];
                foreach ($x['columns'] as $z => $d) {
                    if (! $Kf[$d]) {
                        continue 2;
                    }$e[] = $Kf[$d].($x['descs'][$z] ? ' DESC' : '');
                }if (! $fc[$fe]) {
                    if ($x['type'] != 'PRIMARY' || ! $mg) {
                        $y[] = [$x['type'], $fe, $e];
                    }
                }
            }foreach ($y as $z => $X) {
                if ($X[0] == 'PRIMARY') {
                    unset($y[$z]);
                    $cd[] = '  PRIMARY KEY ('.implode(', ', $X[2]).')';
                }
            }foreach (foreign_keys($R) as $fe => $r) {
                foreach ($r['source'] as $z => $d) {
                    if (! $Kf[$d]) {
                        continue 2;
                    }$r['source'][$z] = idf_unescape($Kf[$d]);
                }if (! isset($cd[" $fe"])) {
                    $cd[] = ' '.format_foreign_key($r);
                }
            }queries('BEGIN');
        }foreach ($p as $z => $o) {
            if (preg_match('~GENERATED~', $o[3])) {
                unset($Kf[array_search($o[0], $Kf)]);
            }$p[$z] = '  '.implode($o);
        }$p = array_merge($p, array_filter($cd));
        foreach ($m->checkConstraints($R) as $Va) {
            if ($Va != $dc) {
                $p[] = "  CHECK ($Va)";
            }
        }if ($na) {
            $p[] = "  CHECK ($na)";
        }$Yh = ($R == $C ? "adminer_$C" : $C);
        if (! queries('CREATE TABLE '.table($Yh)." (\n".implode(",\n", $p)."\n)")) {
            return false;
        }if ($R != '') {
            if ($Kf && ! queries('INSERT INTO '.table($Yh).' ('.implode(', ', $Kf).') SELECT '.implode(', ', array_map('Adminer\idf_escape', array_keys($Kf))).' FROM '.table($R))) {
                return false;
            }$yi = [];
            foreach (triggers($R) as $wi => $fi) {
                $vi = trigger($wi);
                $yi[] = 'CREATE TRIGGER '.idf_escape($wi).' '.implode(' ', $fi).' ON '.table($C)."\n$vi[Statement]";
            }$Ba = $Ba ? 0 : get_val('SELECT seq FROM sqlite_sequence WHERE name = '.q($R));
            if (! queries('DROP TABLE '.table($R)) || ($R == $C && ! queries('ALTER TABLE '.table($Yh).' RENAME TO '.table($C))) || ! alter_indexes($C, $y)) {
                return false;
            }if ($Ba) {
                queries("UPDATE sqlite_sequence SET seq = $Ba WHERE name = ".q($C));
            }foreach ($yi as $vi) {
                if (! queries($vi)) {
                    return false;
                }
            }queries('COMMIT');
        }

return true;
    }function index_sql($R, $U, $C, $e)
    {
        return "CREATE $U ".($U != 'INDEX' ? 'INDEX ' : '').idf_escape($C != '' ? $C : uniqid($R.'_')).' ON '.table($R)." $e";
    }function alter_indexes($R, $c)
    {
        foreach ($c as $G) {
            if ($G[0] == 'PRIMARY') {
                return recreate_table($R, $R, [], [], [], 0, $c);
            }
        }foreach (array_reverse($c) as $X) {
            if (! queries($X[2] == 'DROP' ? 'DROP INDEX '.idf_escape($X[1]) : index_sql($R, $X[0], $X[1], '('.implode(', ', $X[2]).')'))) {
                return false;
            }
        }

return true;
    }function truncate_tables($T)
    {
        return apply_queries('DELETE FROM', $T);
    }function drop_views($Zi)
    {
        return apply_queries('DROP VIEW', $Zi);
    }function drop_tables($T)
    {
        return apply_queries('DROP TABLE', $T);
    }function move_tables($T, $Zi, $Wh)
    {
        return false;
    }function trigger($C)
    {
        if ($C == '') {
            return ['Statement' => "BEGIN\n\t;\nEND"];
        }$w = '(?:[^`"\s]+|`[^`]*`|"[^"]*")+';
        $xi = trigger_options();
        preg_match("~^CREATE\\s+TRIGGER\\s*$w\\s*(".implode('|', $xi['Timing']).")\\s+([a-z]+)(?:\\s+OF\\s+($w))?\\s+ON\\s*$w\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is", get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($C)), $B);
        $if = $B[3];

        return ['Timing' => strtoupper($B[1]), 'Event' => strtoupper($B[2]).($if ? ' OF' : ''), 'Of' => idf_unescape($if), 'Trigger' => $C, 'Statement' => $B[4]];
    }function triggers($R)
    {
        $J = [];
        $xi = trigger_options();
        foreach (get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R)) as $K) {
            preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode('|', $xi['Timing']).')\s*(.*?)\s+ON\b~i', $K['sql'], $B);
            $J[$K['name']] = [$B[1], $B[2]];
        }

return $J;
    }function trigger_options()
    {
        return ['Timing' => ['BEFORE', 'AFTER', 'INSTEAD OF'], 'Event' => ['INSERT', 'UPDATE', 'UPDATE OF', 'DELETE'], 'Type' => ['FOR EACH ROW']];
    }function begin()
    {
        return queries('BEGIN');
    }function last_id()
    {
        return get_val('SELECT LAST_INSERT_ROWID()');
    }function explain($g, $H)
    {
        return $g->query("EXPLAIN QUERY PLAN $H");
    }function found_rows($S, $Z) {}function types()
    {
        return [];
    }function create_sql($R, $Ba, $Gh)
    {
        $J = get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($R));
        foreach (indexes($R) as $C => $x) {
            if ($C == '') {
                continue;
            }$J .= ";\n\n".index_sql($R, $x['type'], $C, '('.implode(', ', array_map('Adminer\idf_escape', $x['columns'])).')');
        }

return $J;
    }function truncate_sql($R)
    {
        return 'DELETE FROM '.table($R);
    }function use_sql($Kb) {}function trigger_sql($R)
    {
        return implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R)));
    }function show_variables()
    {
        $J = [];
        foreach (get_rows('PRAGMA pragma_list') as $K) {
            $C = $K['name'];
            if ($C != 'pragma_list' && $C != 'compile_options') {
                foreach (get_rows("PRAGMA $C") as $K) {
                    $J[$C] .= implode(', ', $K)."\n";
                }
            }
        }

return $J;
    }function show_status()
    {
        $J = [];
        foreach (get_vals('PRAGMA compile_options') as $xf) {
            [$z, $X] = explode('=', $xf, 2);
            $J[$z] = $X;
        }

return $J;
    }function convert_field($o) {}function unconvert_field($o, $J)
    {
        return $J;
    }function support($Pc)
    {
        return preg_match('~^(check|columns|database|drop_col|dump|indexes|descidx|move_col|sql|status|table|trigger|variables|view|view_trigger)$~', $Pc);
    }
}$bc['pgsql'] = 'PostgreSQL';
if (isset($_GET['pgsql'])) {
    define('Adminer\DRIVER', 'pgsql');
    if (extension_loaded('pgsql')) {
        class Db
        {
            public $extension = 'PgSQL';

            public $server_infovar;

            public $affected_rowsvar;

            public $errorvar;

            public $timeoutvar;

            private $link;

            public $resultprivate;

            public $stringprivate;

            public $databaseprivate = true;

            public function _error($yc, $n)
            {
                if (ini_bool('html_errors')) {
                    $n = html_entity_decode(strip_tags($n));
                }$n = preg_replace('~^[^:]*: ~', '', $n);
                $this->error = $n;
            }

            public function connect($N, $V, $F)
            {
                global $b;
                $k = $b->database();
                set_error_handler([$this, '_error']);
                $this->string = "host='".str_replace(':', "' port='", addcslashes($N, "'\\"))."' user='".addcslashes($V, "'\\")."' password='".addcslashes($F, "'\\")."'";
                $Bh = $b->connectSsl();
                if (isset($Bh['mode'])) {
                    $this->string .= " sslmode='".$Bh['mode']."'";
                }$this->link = @pg_connect("$this->string dbname='".($k != '' ? addcslashes($k, "'\\") : 'postgres')."'", PGSQL_CONNECT_FORCE_NEW);
                if (! $this->link && $k != '') {
                    $this->database = false;
                    $this->link = @pg_connect("$this->string dbname='postgres'", PGSQL_CONNECT_FORCE_NEW);
                }restore_error_handler();
                if ($this->link) {
                    $Xi = pg_version($this->link);
                    $this->server_info = $Xi['server'];
                    pg_set_client_encoding($this->link, 'UTF8');
                }

return (bool) $this->link;
            }

            public function quote($Q)
            {
                return function_exists('pg_escape_literal') ? pg_escape_literal($this->link, $Q) : "'".pg_escape_string($this->link, $Q)."'";
            }

            public function value($X, $o)
            {
                return $o['type'] == 'bytea' && $X !== null ? pg_unescape_bytea($X) : $X;
            }

            public function select_db($Kb)
            {
                global $b;
                if ($Kb == $b->database()) {
                    return $this->database;
                }$J = @pg_connect("$this->string dbname='".addcslashes($Kb, "'\\")."'", PGSQL_CONNECT_FORCE_NEW);
                if ($J) {
                    $this->link = $J;
                }

return $J;
            }

            public function close()
            {
                $this->link = @pg_connect("$this->string dbname='postgres'");
            }

            public function query($H, $Bi = false)
            {
                $I = @pg_query($this->link, $H);
                $this->error = '';
                if (! $I) {
                    $this->error = pg_last_error($this->link);
                    $J = false;
                } elseif (! pg_num_fields($I)) {
                    $this->affected_rows = pg_affected_rows($I);
                    $J = true;
                } else {
                    $J = new Result($I);
                }if ($this->timeout) {
                    $this->timeout = 0;
                    $this->query('RESET statement_timeout');
                }

return $J;
            }

            public function multi_query($H)
            {
                return $this->result = $this->query($H);
            }

            public function store_result()
            {
                return $this->result;
            }

            public function next_result()
            {
                return false;
            }

            public function result($H, $o = 0)
            {
                $I = $this->query($H);

                return $I ? $I->fetch_column($o) : false;
            }

            public function warnings()
            {
                return h(pg_last_notice($this->link));
            }
        }class Result
        {
            public $num_rows;

            private $result;

            public $offsetprivate = 0;

            public function __construct($I)
            {
                $this->result = $I;
                $this->num_rows = pg_num_rows($I);
            }

            public function fetch_assoc()
            {
                return pg_fetch_assoc($this->result);
            }

            public function fetch_row()
            {
                return pg_fetch_row($this->result);
            }

            public function fetch_column($o)
            {
                return $this->num_rows ? pg_fetch_result($this->result, 0, $o) : false;
            }

            public function fetch_field()
            {
                $d = $this->offset++;
                $J = new \stdClass;
                if (function_exists('pg_field_table')) {
                    $J->orgtable = pg_field_table($this->result, $d);
                }$J->name = pg_field_name($this->result, $d);
                $J->orgname = $J->name;
                $J->type = pg_field_type($this->result, $d);
                $J->charsetnr = ($J->type == 'bytea' ? 63 : 0);

                return $J;
            }

            public function __destruct()
            {
                pg_free_result($this->result);
            }
        }
    } elseif (extension_loaded('pdo_pgsql')) {
        class Db extends PdoDb
        {
            public $extension = 'PDO_PgSQL';

            public $timeoutvar;

            public function connect($N, $V, $F)
            {
                global $b;
                $k = $b->database();
                $hc = "pgsql:host='".str_replace(':', "' port='", addcslashes($N, "'\\"))."' client_encoding=utf8 dbname='".($k != '' ? addcslashes($k, "'\\") : 'postgres')."'";
                $Bh = $b->connectSsl();
                if (isset($Bh['mode'])) {
                    $hc .= " sslmode='".$Bh['mode']."'";
                }$this->dsn($hc, $V, $F);

                return true;
            }

            public function select_db($Kb)
            {
                global $b;

                return $b->database() == $Kb;
            }

            public function query($H, $Bi = false)
            {
                $J = parent::query($H, $Bi);
                if ($this->timeout) {
                    $this->timeout = 0;
                    parent::query('RESET statement_timeout');
                }

return $J;
            }

            public function warnings()
            {
                return '';
            }

            public function close() {}
        }
    }class Driver extends SqlDriver
    {
        public static $ig = ['PgSQL', 'PDO_PgSQL'];

        public static $de = 'pgsql';

        public $operators = ['=', '<', '>', '<=', '>=', '!=', '~', '!~', 'LIKE', 'LIKE %%', 'ILIKE', 'ILIKE %%', 'IN', 'IS NULL', 'NOT LIKE', 'NOT IN', 'IS NOT NULL'];

        public $functions = ['char_length', 'lower', 'round', 'to_hex', 'to_timestamp', 'upper'];

        public $grouping = ['avg', 'count', 'count distinct', 'max', 'min', 'sum'];

        public function __construct($g)
        {
            parent::__construct($g);
            $this->types = [lang(25) => ['smallint' => 5, 'integer' => 10, 'bigint' => 19, 'boolean' => 1, 'numeric' => 0, 'real' => 7, 'double precision' => 16, 'money' => 20], lang(26) => ['date' => 13, 'time' => 17, 'timestamp' => 20, 'timestamptz' => 21, 'interval' => 0], lang(27) => ['character' => 0, 'character varying' => 0, 'text' => 0, 'tsquery' => 0, 'tsvector' => 0, 'uuid' => 0, 'xml' => 0], lang(28) => ['bit' => 0, 'bit varying' => 0, 'bytea' => 0], lang(29) => ['cidr' => 43, 'inet' => 43, 'macaddr' => 17, 'macaddr8' => 23, 'txid_snapshot' => 0], lang(30) => ['box' => 0, 'circle' => 0, 'line' => 0, 'lseg' => 0, 'path' => 0, 'point' => 0, 'polygon' => 0]];
            if (min_version(9.2, 0, $g)) {
                $this->types[lang(27)]['json'] = 4294967295;
                if (min_version(9.4, 0, $g)) {
                    $this->types[lang(27)]['jsonb'] = 4294967295;
                }
            }$this->editFunctions = [['char' => 'md5', 'date|time' => 'now'], [number_type() => '+/-', 'date|time' => '+ interval/- interval', 'char|text' => '||']];
            if (min_version(12, 0, $g)) {
                $this->generated = ['STORED'];
            }
        }

        public function enumLength($o)
        {
            $uc = $this->types[lang(31)][$o['type']];

            return $uc ? type_values($uc) : '';
        }

        public function setUserTypes($Ai)
        {
            $this->types[lang(31)] = array_flip($Ai);
        }

        public function insertUpdate($R, $L, $G)
        {
            global $g;
            foreach ($L as $O) {
                $Ji = [];
                $Z = [];
                foreach ($O as $z => $X) {
                    $Ji[] = "$z = $X";
                    if (isset($G[idf_unescape($z)])) {
                        $Z[] = "$z = $X";
                    }
                }if (! (($Z && queries('UPDATE '.table($R).' SET '.implode(', ', $Ji).' WHERE '.implode(' AND ', $Z)) && $g->affected_rows) || queries('INSERT INTO '.table($R).' ('.implode(', ', array_keys($O)).') VALUES ('.implode(', ', $O).')'))) {
                    return false;
                }
            }

return true;
        }

        public function slowQuery($H, $ei)
        {
            $this->conn->query('SET statement_timeout = '.(1000 * $ei));
            $this->conn->timeout = 1000 * $ei;

            return $H;
        }

        public function convertSearch($w, $X, $o)
        {
            $bi = 'char|text';
            if (strpos($X['op'], 'LIKE') === false) {
                $bi .= '|date|time(stamp)?|boolean|uuid|inet|cidr|macaddr|'.number_type();
            }

return preg_match("~$bi~", $o['type']) ? $w : "CAST($w AS text)";
        }

        public function quoteBinary($Tg)
        {
            return "'\\x".bin2hex($Tg)."'";
        }

        public function warnings()
        {
            return $this->conn->warnings();
        }

        public function tableHelp($C, $be = false)
        {
            $we = ['information_schema' => 'infoschema', 'pg_catalog' => ($be ? 'view' : 'catalog')];
            $A = $we[$_GET['ns']];
            if ($A) {
                return "$A-".str_replace('_', '-', $C).'.html';
            }
        }

        public function supportsIndex($S)
        {
            return $S['Engine'] != 'view';
        }

        public function hasCStyleEscapes()
        {
            static $Qa;
            if ($Qa === null) {
                $Qa = ($this->conn->result('SHOW standard_conforming_strings') == 'off');
            }

return $Qa;
        }
    }function idf_escape($w)
    {
        return '"'.str_replace('"', '""', $w).'"';
    }function table($w)
    {
        return idf_escape($w);
    }function connect($Cb)
    {
        global $bc;
        $g = new Db;
        if ($g->connect($Cb[0], $Cb[1], $Cb[2])) {
            if (min_version(9, 0, $g)) {
                $g->query("SET application_name = 'Adminer'");
            }$_b = $g->result('SHOW crdb_version');
            $g->server_info .= ($_b ? '-'.preg_replace('~ \(.*~', '', $_b) : '');
            $g->cockroach = preg_match('~CockroachDB~', $g->server_info);
            if ($g->cockroach) {
                $bc[DRIVER] = 'CockroachDB';
            }

return $g;
        }

return $g->error;
    }function get_databases()
    {
        return
        get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");
    }function limit($H, $Z, $_, $D = 0, $fh = ' ')
    {
        return " $H$Z".($_ !== null ? $fh."LIMIT $_".($D ? " OFFSET $D" : '') : '');
    }function limit1($R, $H, $Z, $fh = "\n")
    {
        return preg_match('~^INTO~', $H) ? limit($H, $Z, 1, 0, $fh) : " $H".(is_view(table_status1($R)) ? $Z : $fh.'WHERE ctid = (SELECT ctid FROM '.table($R).$Z.$fh.'LIMIT 1)');
    }function db_collation($k, $ib)
    {
        return get_val('SELECT datcollate FROM pg_database WHERE datname = '.q($k));
    }function engines()
    {
        return [];
    }function logged_user()
    {
        return get_val('SELECT user');
    }function tables_list()
    {
        $H = 'SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()';
        if (support('materializedview')) {
            $H .= "
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";
        }$H .= '
ORDER BY 1';

        return get_key_vals($H);
    }function count_tables($j)
    {
        global $g;
        $J = [];
        foreach ($j as $k) {
            if ($g->select_db($k)) {
                $J[$k] = count(tables_list());
            }
        }

return $J;
    }function table_status($C = '')
    {
        static $xd;
        if ($xd === null) {
            $xd = get_val("SELECT 'pg_table_size'::regproc");
        }$J = [];
        foreach (get_rows("SELECT
	c.relname AS \"Name\",
	CASE c.relkind WHEN 'r' THEN 'table' WHEN 'm' THEN 'materialized view' ELSE 'view' END AS \"Engine\"".($xd ? ',
	pg_table_size(c.oid) AS "Data_length",
	pg_indexes_size(c.oid) AS "Index_length"' : '').",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12) ? "''" : "CASE WHEN c.relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	c.reltuples as \"Rows\",
	n.nspname
FROM pg_class c
JOIN pg_namespace n ON(n.nspname = current_schema() AND n.oid = c.relnamespace)
WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
".($C != '' ? 'AND relname = '.q($C) : 'ORDER BY relname')) as $K) {
            $J[$K['Name']] = $K;
        }

return $C != '' ? $J[$C] : $J;
    }function is_view($S)
    {
        return in_array($S['Engine'], ['view', 'materialized view']);
    }function fk_support($S)
    {
        return true;
    }function fields($R)
    {
        $J = [];
        $ua = ['timestamp without time zone' => 'timestamp', 'timestamp with time zone' => 'timestamptz'];
        foreach (get_rows('SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	col_description(c.oid, a.attnum) AS comment'.(min_version(10) ? ',
	a.attidentity'.(min_version(12) ? ',
	a.attgenerated' : '') : '').'
FROM pg_class c
JOIN pg_namespace n ON c.relnamespace = n.oid
JOIN pg_attribute a ON c.oid = a.attrelid
LEFT JOIN pg_attrdef d ON c.oid = d.adrelid AND a.attnum = d.adnum
WHERE c.relname = '.q($R).'
AND n.nspname = current_schema()
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum') as $K) {
            preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~', $K['full_type'], $B);
            [, $U, $te, $K['length'], $oa, $xa] = $B;
            $K['length'] .= $xa;
            $Xa = $U.$oa;
            if (isset($ua[$Xa])) {
                $K['type'] = $ua[$Xa];
                $K['full_type'] = $K['type'].$te.$xa;
            } else {
                $K['type'] = $U;
                $K['full_type'] = $K['type'].$te.$oa.$xa;
            }if (in_array($K['attidentity'], ['a', 'd'])) {
                $K['default'] = 'GENERATED '.($K['attidentity'] == 'd' ? 'BY DEFAULT' : 'ALWAYS').' AS IDENTITY';
            }$K['generated'] = ($K['attgenerated'] == 's' ? 'STORED' : '');
            $K['null'] = ! $K['attnotnull'];
            $K['auto_increment'] = $K['attidentity'] || preg_match('~^nextval\(~i', $K['default']) || preg_match('~^unique_rowid\(~', $K['default']);
            $K['privileges'] = ['insert' => 1, 'select' => 1, 'update' => 1, 'where' => 1, 'order' => 1];
            if (preg_match('~(.+)::[^,)]+(.*)~', $K['default'], $B)) {
                $K['default'] = ($B[1] == 'NULL' ? null : idf_unescape($B[1]).$B[2]);
            }$J[$K['field']] = $K;
        }

return $J;
    }function indexes($R, $h = null)
    {
        global $g;
        if (! is_object($h)) {
            $h = $g;
        }$J = [];
        $Ph = $h->result('SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = '.q($R));
        $e = get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Ph AND attnum > 0", $h);
        foreach (get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, (indpred IS NOT NULL)::int as indispartial
FROM pg_index i, pg_class ci
WHERE i.indrelid = $Ph AND ci.oid = i.indexrelid
ORDER BY indisprimary DESC, indisunique DESC", $h) as $K) {
            $Gg = $K['relname'];
            $J[$Gg]['type'] = ($K['indispartial'] ? 'INDEX' : ($K['indisprimary'] ? 'PRIMARY' : ($K['indisunique'] ? 'UNIQUE' : 'INDEX')));
            $J[$Gg]['columns'] = [];
            $J[$Gg]['descs'] = [];
            if ($K['indkey']) {
                foreach (explode(' ', $K['indkey']) as $Nd) {
                    $J[$Gg]['columns'][] = $e[$Nd];
                }foreach (explode(' ', $K['indoption']) as $Od) {
                    $J[$Gg]['descs'][] = ($Od & 1 ? '1' : null);
                }
            }$J[$Gg]['lengths'] = [];
        }

return $J;
    }function foreign_keys($R)
    {
        global $m;
        $J = [];
        foreach (get_rows('SELECT conname, condeferrable::int AS deferrable, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = (SELECT pc.oid FROM pg_class AS pc INNER JOIN pg_namespace AS pn ON (pn.oid = pc.relnamespace) WHERE pc.relname = '.q($R)." AND pn.nspname = current_schema())
AND contype = 'f'::char
ORDER BY conkey, conname") as $K) {
            if (preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA', $K['definition'], $B)) {
                $K['source'] = array_map('Adminer\idf_unescape', array_map('trim', explode(',', $B[1])));
                if (preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~', $B[2], $Be)) {
                    $K['ns'] = idf_unescape($Be[2]);
                    $K['table'] = idf_unescape($Be[4]);
                }$K['target'] = array_map('Adminer\idf_unescape', array_map('trim', explode(',', $B[3])));
                $K['on_delete'] = (preg_match("~ON DELETE ($m->onActions)~", $B[4], $Be) ? $Be[1] : 'NO ACTION');
                $K['on_update'] = (preg_match("~ON UPDATE ($m->onActions)~", $B[4], $Be) ? $Be[1] : 'NO ACTION');
                $J[$K['conname']] = $K;
            }
        }

return $J;
    }function view($C)
    {
        return ['select' => trim(get_val('SELECT pg_get_viewdef('.get_val('SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = '.q($C)).')'))];
    }function collations()
    {
        return [];
    }function information_schema($k)
    {
        return get_schema() == 'information_schema';
    }function error()
    {
        global $g;
        $J = h($g->error);
        if (preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s', $J, $B)) {
            $J = $B[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($B[3]).'})(.*)~', '\1<b>\2</b>', $B[2]).$B[4];
        }

return nl_br($J);
    }function create_database($k, $hb)
    {
        return queries('CREATE DATABASE '.idf_escape($k).($hb ? ' ENCODING '.idf_escape($hb) : ''));
    }function drop_databases($j)
    {
        global $g;
        $g->close();

        return apply_queries('DROP DATABASE', $j, 'Adminer\idf_escape');
    }function rename_database($C, $hb)
    {
        global $g;
        $g->close();

        return queries('ALTER DATABASE '.idf_escape(DB).' RENAME TO '.idf_escape($C));
    }function auto_increment()
    {
        return '';
    }function alter_table($R, $C, $p, $cd, $nb, $sc, $hb, $Ba, $Wf)
    {
        $c = [];
        $vg = [];
        if ($R != '' && $R != $C) {
            $vg[] = 'ALTER TABLE '.table($R).' RENAME TO '.table($C);
        }$gh = '';
        foreach ($p as $o) {
            $d = idf_escape($o[0]);
            $X = $o[1];
            if (! $X) {
                $c[] = "DROP $d";
            } else {
                $Ti = $X[5];
                unset($X[5]);
                if ($o[0] == '') {
                    if (isset($X[6])) {
                        $X[1] = ($X[1] == ' bigint' ? ' big' : ($X[1] == ' smallint' ? ' small' : ' ')).'serial';
                    }$c[] = ($R != '' ? 'ADD ' : '  ').implode($X);
                    if (isset($X[6])) {
                        $c[] = ($R != '' ? 'ADD' : ' ')." PRIMARY KEY ($X[0])";
                    }
                } else {
                    if ($d != $X[0]) {
                        $vg[] = 'ALTER TABLE '.table($C)." RENAME $d TO $X[0]";
                    }$c[] = "ALTER $d TYPE$X[1]";
                    $hh = $R.'_'.idf_unescape($X[0]).'_seq';
                    $c[] = "ALTER $d ".($X[3] ? 'SET'.preg_replace('~GENERATED ALWAYS(.*) STORED~', 'EXPRESSION\1', $X[3]) : (isset($X[6]) ? 'SET DEFAULT nextval('.q($hh).')' : 'DROP DEFAULT'));
                    if (isset($X[6])) {
                        $gh = 'CREATE SEQUENCE IF NOT EXISTS '.idf_escape($hh).' OWNED BY '.idf_escape($R).".$X[0]";
                    }$c[] = "ALTER $d ".($X[2] == ' NULL' ? 'DROP NOT' : 'SET').$X[2];
                }if ($o[0] != '' || $Ti != '') {
                    $vg[] = 'COMMENT ON COLUMN '.table($C).".$X[0] IS ".($Ti != '' ? substr($Ti, 9) : "''");
                }
            }
        }$c = array_merge($c, $cd);
        if ($R == '') {
            array_unshift($vg, 'CREATE TABLE '.table($C)." (\n".implode(",\n", $c)."\n)");
        } elseif ($c) {
            array_unshift($vg, 'ALTER TABLE '.table($R)."\n".implode(",\n", $c));
        }if ($gh) {
            array_unshift($vg, $gh);
        }if ($nb !== null) {
            $vg[] = 'COMMENT ON TABLE '.table($C).' IS '.q($nb);
        }foreach ($vg as $H) {
            if (! queries($H)) {
                return false;
            }
        }

return true;
    }function alter_indexes($R, $c)
    {
        $i = [];
        $cc = [];
        $vg = [];
        foreach ($c as $X) {
            if ($X[0] != 'INDEX') {
                $i[] = ($X[2] == 'DROP' ? "\nDROP CONSTRAINT ".idf_escape($X[1]) : "\nADD".($X[1] != '' ? ' CONSTRAINT '.idf_escape($X[1]) : '')." $X[0] ".($X[0] == 'PRIMARY' ? 'KEY ' : '').'('.implode(', ', $X[2]).')');
            } elseif ($X[2] == 'DROP') {
                $cc[] = idf_escape($X[1]);
            } else {
                $vg[] = 'CREATE INDEX '.idf_escape($X[1] != '' ? $X[1] : uniqid($R.'_')).' ON '.table($R).' ('.implode(', ', $X[2]).')';
            }
        }if ($i) {
            array_unshift($vg, 'ALTER TABLE '.table($R).implode(',', $i));
        }if ($cc) {
            array_unshift($vg, 'DROP INDEX '.implode(', ', $cc));
        }foreach ($vg as $H) {
            if (! queries($H)) {
                return false;
            }
        }

return true;
    }function truncate_tables($T)
    {
        return queries('TRUNCATE '.implode(', ', array_map('Adminer\table', $T)));
    }function drop_views($Zi)
    {
        return drop_tables($Zi);
    }function drop_tables($T)
    {
        foreach ($T as $R) {
            $P = table_status($R);
            if (! queries('DROP '.strtoupper($P['Engine']).' '.table($R))) {
                return false;
            }
        }

return true;
    }function move_tables($T, $Zi, $Wh)
    {
        foreach (array_merge($T, $Zi) as $R) {
            $P = table_status($R);
            if (! queries('ALTER '.strtoupper($P['Engine']).' '.table($R).' SET SCHEMA '.idf_escape($Wh))) {
                return false;
            }
        }

return true;
    }function trigger($C, $R)
    {
        if ($C == '') {
            return ['Statement' => 'EXECUTE PROCEDURE ()'];
        }$e = [];
        $Z = 'WHERE trigger_schema = current_schema() AND event_object_table = '.q($R).' AND trigger_name = '.q($C);
        foreach (get_rows("SELECT * FROM information_schema.triggered_update_columns $Z") as $K) {
            $e[] = $K['event_object_column'];
        }$J = [];
        foreach (get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC") as $K) {
            if ($e && $K['Event'] == 'UPDATE') {
                $K['Event'] .= ' OF';
            }$K['Of'] = implode(', ', $e);
            if ($J) {
                $K['Event'] .= " OR $J[Event]";
            }$J = $K;
        }

return $J;
    }function triggers($R)
    {
        $J = [];
        foreach (get_rows('SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = '.q($R)) as $K) {
            $vi = trigger($K['trigger_name'], $R);
            $J[$vi['Trigger']] = [$vi['Timing'], $vi['Event']];
        }

return $J;
    }function trigger_options()
    {
        return ['Timing' => ['BEFORE', 'AFTER'], 'Event' => ['INSERT', 'UPDATE', 'UPDATE OF', 'DELETE', 'INSERT OR UPDATE', 'INSERT OR UPDATE OF', 'DELETE OR INSERT', 'DELETE OR UPDATE', 'DELETE OR UPDATE OF', 'DELETE OR INSERT OR UPDATE', 'DELETE OR INSERT OR UPDATE OF'], 'Type' => ['FOR EACH ROW', 'FOR EACH STATEMENT']];
    }function routine($C, $U)
    {
        $L = get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = '.q($C));
        $J = $L[0];
        $J['returns'] = ['type' => $J['type_udt_name']];
        $J['fields'] = get_rows('SELECT parameter_name AS field, data_type AS type, character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = '.q($C).'
ORDER BY ordinal_position');

        return $J;
    }function routines()
    {
        return
        get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()
ORDER BY SPECIFIC_NAME');
    }function routine_languages()
    {
        return get_vals('SELECT LOWER(lanname) FROM pg_catalog.pg_language');
    }function routine_id($C, $K)
    {
        $J = [];
        foreach ($K['fields'] as $o) {
            $J[] = $o['type'];
        }

return idf_escape($C).'('.implode(', ', $J).')';
    }function last_id()
    {
        return 0;
    }function explain($g, $H)
    {
        return $g->query("EXPLAIN $H");
    }function found_rows($S, $Z)
    {
        if (preg_match('~ rows=([0-9]+)~', get_val('EXPLAIN SELECT * FROM '.idf_escape($S['Name']).($Z ? ' WHERE '.implode(' AND ', $Z) : '')), $Fg)) {
            return $Fg[1];
        }

return false;
    }function types()
    {
        return
        get_key_vals("SELECT oid, typname
FROM pg_type
WHERE typnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema())
AND typtype IN ('b','d','e')
AND typelem = 0");
    }function type_values($v)
    {
        $xc = get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $v ORDER BY enumsortorder");

        return $xc ? "'".implode("', '", array_map('addslashes', $xc))."'" : '';
    }function schemas()
    {
        return get_vals('SELECT nspname FROM pg_namespace ORDER BY nspname');
    }function get_schema()
    {
        return get_val('SELECT current_schema()');
    }function set_schema($Vg, $h = null)
    {
        global $g,$m;
        if (! $h) {
            $h = $g;
        }$J = $h->query('SET search_path TO '.idf_escape($Vg));
        $m->setUserTypes(types());

        return $J;
    }function foreign_keys_sql($R)
    {
        $J = '';
        $P = table_status($R);
        $Zc = foreign_keys($R);
        ksort($Zc);
        foreach ($Zc as $Yc => $Xc) {
            $J .= 'ALTER TABLE ONLY '.idf_escape($P['nspname']).'.'.idf_escape($P['Name']).' ADD CONSTRAINT '.idf_escape($Yc)." $Xc[definition] ".($Xc['deferrable'] ? 'DEFERRABLE' : 'NOT DEFERRABLE').";\n";
        }

return $J ? "$J\n" : $J;
    }function create_sql($R, $Ba, $Gh)
    {
        global $m;
        $Lg = [];
        $ih = [];
        $P = table_status($R);
        if (is_view($P)) {
            $Yi = view($R);

            return rtrim('CREATE VIEW '.idf_escape($R)." AS $Yi[select]", ';');
        }$p = fields($R);
        if (! $P || empty($p)) {
            return false;
        }$J = 'CREATE TABLE '.idf_escape($P['nspname']).'.'.idf_escape($P['Name'])." (\n    ";
        foreach ($p as $o) {
            $Tf = idf_escape($o['field']).' '.$o['full_type'].default_value($o).($o['attnotnull'] ? ' NOT NULL' : '');
            $Lg[] = $Tf;
            if (preg_match('~nextval\(\'([^\']+)\'\)~', $o['default'], $De)) {
                $hh = $De[1];
                $wh = reset(get_rows((min_version(10) ? 'SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = '.q(idf_unescape($hh)) : "SELECT * FROM $hh"), null, '-- '));
                $ih[] = ($Gh == 'DROP+CREATE' ? "DROP SEQUENCE IF EXISTS $hh;\n" : '')."CREATE SEQUENCE $hh INCREMENT $wh[increment_by] MINVALUE $wh[min_value] MAXVALUE $wh[max_value]".($Ba && $wh['last_value'] ? ' START '.($wh['last_value'] + 1) : '')." CACHE $wh[cache_value];";
            }
        }if (! empty($ih)) {
            $J = implode("\n\n", $ih)."\n\n$J";
        }$G = '';
        foreach (indexes($R) as $Ld => $x) {
            if ($x['type'] == 'PRIMARY') {
                $G = $Ld;
                $Lg[] = 'CONSTRAINT '.idf_escape($Ld).' PRIMARY KEY ('.implode(', ', array_map('Adminer\idf_escape', $x['columns'])).')';
            }
        }foreach ($m->checkConstraints($R) as $sb => $ub) {
            $Lg[] = 'CONSTRAINT '.idf_escape($sb)." CHECK $ub";
        }$J .= implode(",\n    ", $Lg)."\n) WITH (oids = ".($P['Oid'] ? 'true' : 'false').');';
        if ($P['Comment']) {
            $J .= "\n\nCOMMENT ON TABLE ".idf_escape($P['nspname']).'.'.idf_escape($P['Name']).' IS '.q($P['Comment']).';';
        }foreach ($p as $Rc => $o) {
            if ($o['comment']) {
                $J .= "\n\nCOMMENT ON COLUMN ".idf_escape($P['nspname']).'.'.idf_escape($P['Name']).'.'.idf_escape($Rc).' IS '.q($o['comment']).';';
            }
        }foreach (get_rows('SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = '.q($R).($G ? ' AND indexname != '.q($G) : ''), null, '-- ') as $K) {
            $J .= "\n\n$K[indexdef];";
        }

return rtrim($J, ';');
    }function truncate_sql($R)
    {
        return 'TRUNCATE '.table($R);
    }function trigger_sql($R)
    {
        $P = table_status($R);
        $J = '';
        foreach (triggers($R) as $ui => $ti) {
            $vi = trigger($ui, $P['Name']);
            $J .= "\nCREATE TRIGGER ".idf_escape($vi['Trigger'])." $vi[Timing] $vi[Event] ON ".idf_escape($P['nspname']).'.'.idf_escape($P['Name'])." $vi[Type] $vi[Statement];;\n";
        }

return $J;
    }function use_sql($Kb)
    {
        return "\connect ".idf_escape($Kb);
    }function show_variables()
    {
        return get_key_vals('SHOW ALL');
    }function process_list()
    {
        return get_rows('SELECT * FROM pg_stat_activity ORDER BY '.(min_version(9.2) ? 'pid' : 'procpid'));
    }function convert_field($o) {}function unconvert_field($o, $J)
    {
        return $J;
    }function support($Pc)
    {
        global $g;

        return preg_match('~^(check|database|table|columns|sql|indexes|descidx|comment|view|'.(min_version(9.3) ? 'materializedview|' : '').'scheme|routine|sequence|trigger|type|variables|drop_col'.($g->cockroach ? '' : '|processlist').'|kill|dump)$~', $Pc);
    }function kill_process($X)
    {
        return queries('SELECT pg_terminate_backend('.number($X).')');
    }function connection_id()
    {
        return 'SELECT pg_backend_pid()';
    }function max_connections()
    {
        return get_val('SHOW max_connections');
    }
}$bc['oracle'] = 'Oracle (beta)';
if (isset($_GET['oracle'])) {
    define('Adminer\DRIVER', 'oracle');
    if (extension_loaded('oci8')) {
        class Db
        {
            public $extension = 'oci8';

            public $server_infovar;

            public $affected_rowsvar;

            public $errnovar;

            public $errorvar;

            public $_current_db;

            private $link;

            public $resultprivate;

            public function _error($yc, $n)
            {
                if (ini_bool('html_errors')) {
                    $n = html_entity_decode(strip_tags($n));
                }$n = preg_replace('~^[^:]*: ~', '', $n);
                $this->error = $n;
            }

            public function connect($N, $V, $F)
            {
                $this->link = @oci_new_connect($V, $F, $N, 'AL32UTF8');
                if ($this->link) {
                    $this->server_info = oci_server_version($this->link);

                    return true;
                }$n = oci_error();
                $this->error = $n['message'];

                return false;
            }

            public function quote($Q)
            {
                return "'".str_replace("'", "''", $Q)."'";
            }

            public function select_db($Kb)
            {
                $this->_current_db = $Kb;

                return true;
            }

            public function query($H, $Bi = false)
            {
                $I = oci_parse($this->link, $H);
                $this->error = '';
                if (! $I) {
                    $n = oci_error($this->link);
                    $this->errno = $n['code'];
                    $this->error = $n['message'];

                    return false;
                }set_error_handler([$this, '_error']);
                $J = @oci_execute($I);
                restore_error_handler();
                if ($J) {
                    if (oci_num_fields($I)) {
                        return new Result($I);
                    }$this->affected_rows = oci_num_rows($I);
                    oci_free_statement($I);
                }

return $J;
            }

            public function multi_query($H)
            {
                return $this->result = $this->query($H);
            }

            public function store_result()
            {
                return $this->result;
            }

            public function next_result()
            {
                return false;
            }

            public function result($H, $o = 0)
            {
                $I = $this->query($H);

                return is_object($I) ? $I->fetch_column($o) : false;
            }
        }class Result
        {
            public $num_rows;

            private $result;

            public $offsetprivate = 1;

            public function __construct($I)
            {
                $this->result = $I;
            }

            private function convert($K)
            {
                foreach ((array) $K as $z => $X) {
                    if (is_a($X, 'OCI-Lob')) {
                        $K[$z] = $X->load();
                    }
                }

return $K;
            }

            public function fetch_assoc()
            {
                return $this->convert(oci_fetch_assoc($this->result));
            }

            public function fetch_row()
            {
                return $this->convert(oci_fetch_row($this->result));
            }

            public function fetch_column($o)
            {
                return oci_fetch($this->result) ? oci_result($this->result, $o + 1) : false;
            }

            public function fetch_field()
            {
                $d = $this->offset++;
                $J = new \stdClass;
                $J->name = oci_field_name($this->result, $d);
                $J->orgname = $J->name;
                $J->type = oci_field_type($this->result, $d);
                $J->charsetnr = (preg_match('~raw|blob|bfile~', $J->type) ? 63 : 0);

                return $J;
            }

            public function __destruct()
            {
                oci_free_statement($this->result);
            }
        }
    } elseif (extension_loaded('pdo_oci')) {
        class Db extends PdoDb
        {
            public $extension = 'PDO_OCI';

            public $_current_db;

            public function connect($N, $V, $F)
            {
                $this->dsn("oci:dbname=//$N;charset=AL32UTF8", $V, $F);

                return true;
            }

            public function select_db($Kb)
            {
                $this->_current_db = $Kb;

                return true;
            }
        }
    }class Driver extends SqlDriver
    {
        public static $ig = ['OCI8', 'PDO_OCI'];

        public static $de = 'oracle';

        public $editFunctions = [['date' => 'current_date', 'timestamp' => 'current_timestamp'], ['number|float|double' => '+/-', 'date|timestamp' => '+ interval/- interval', 'char|clob' => '||']];

        public $operators = ['=', '<', '>', '<=', '>=', '!=', 'LIKE', 'LIKE %%', 'IN', 'IS NULL', 'NOT LIKE', 'NOT IN', 'IS NOT NULL', 'SQL'];

        public $functions = ['length', 'lower', 'round', 'upper'];

        public $grouping = ['avg', 'count', 'count distinct', 'max', 'min', 'sum'];

        public function __construct($g)
        {
            parent::__construct($g);
            $this->types = [lang(25) => ['number' => 38, 'binary_float' => 12, 'binary_double' => 21], lang(26) => ['date' => 10, 'timestamp' => 29, 'interval year' => 12, 'interval day' => 28], lang(27) => ['char' => 2000, 'varchar2' => 4000, 'nchar' => 2000, 'nvarchar2' => 4000, 'clob' => 4294967295, 'nclob' => 4294967295], lang(28) => ['raw' => 2000, 'long raw' => 2147483648, 'blob' => 4294967295, 'bfile' => 4294967296]];
        }

        public function begin()
        {
            return true;
        }

        public function insertUpdate($R, $L, $G)
        {
            global $g;
            foreach ($L as $O) {
                $Ji = [];
                $Z = [];
                foreach ($O as $z => $X) {
                    $Ji[] = "$z = $X";
                    if (isset($G[idf_unescape($z)])) {
                        $Z[] = "$z = $X";
                    }
                }if (! (($Z && queries('UPDATE '.table($R).' SET '.implode(', ', $Ji).' WHERE '.implode(' AND ', $Z)) && $g->affected_rows) || queries('INSERT INTO '.table($R).' ('.implode(', ', array_keys($O)).') VALUES ('.implode(', ', $O).')'))) {
                    return false;
                }
            }

return true;
        }

        public function hasCStyleEscapes()
        {
            return true;
        }
    }function idf_escape($w)
    {
        return '"'.str_replace('"', '""', $w).'"';
    }function table($w)
    {
        return idf_escape($w);
    }function connect($Cb)
    {
        $g = new Db;
        if ($g->connect($Cb[0], $Cb[1], $Cb[2])) {
            return $g;
        }

return $g->error;
    }function get_databases()
    {
        return
        get_vals('SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1');
    }function limit($H, $Z, $_, $D = 0, $fh = ' ')
    {
        return $D ? " * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $H$Z) t WHERE rownum <= ".($_ + $D).") WHERE rnum > $D" : ($_ !== null ? " * FROM (SELECT $H$Z) WHERE rownum <= ".($_ + $D) : " $H$Z");
    }function limit1($R, $H, $Z, $fh = "\n")
    {
        return " $H$Z";
    }function db_collation($k, $ib)
    {
        return get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");
    }function engines()
    {
        return [];
    }function logged_user()
    {
        return get_val('SELECT USER FROM DUAL');
    }function get_current_db()
    {
        global $g;
        $k = $g->_current_db ?: DB;
        unset($g->_current_db);

        return $k;
    }function where_owner($kg, $Nf = 'owner')
    {
        if (! $_GET['ns']) {
            return '';
        }

return "$kg$Nf = sys_context('USERENV', 'CURRENT_SCHEMA')";
    }function views_table($e)
    {
        $Nf = where_owner('');

        return "(SELECT $e FROM all_views WHERE ".($Nf ?: 'rownum < 0').')';
    }function tables_list()
    {
        $Yi = views_table('view_name');
        $Nf = where_owner(' AND ');

        return
        get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."$Nf
UNION SELECT view_name, 'view' FROM $Yi
ORDER BY 1");
    }function count_tables($j)
    {
        $J = [];
        foreach ($j as $k) {
            $J[$k] = get_val('SELECT COUNT(*) FROM all_tables WHERE tablespace_name = '.q($k));
        }

return $J;
    }function table_status($C = '')
    {
        $J = [];
        $Yg = q($C);
        $k = get_current_db();
        $Yi = views_table('view_name');
        $Nf = where_owner(' AND ');
        foreach (get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = '.q($k).$Nf.($C != '' ? " AND table_name = $Yg" : '')."
UNION SELECT view_name, 'view', 0, 0 FROM $Yi".($C != '' ? " WHERE view_name = $Yg" : '').'
ORDER BY 1') as $K) {
            if ($C != '') {
                return $K;
            }$J[$K['Name']] = $K;
        }

return $J;
    }function is_view($S)
    {
        return $S['Engine'] == 'view';
    }function fk_support($S)
    {
        return true;
    }function fields($R)
    {
        $J = [];
        $Nf = where_owner(' AND ');
        foreach (get_rows('SELECT * FROM all_tab_columns WHERE table_name = '.q($R)."$Nf ORDER BY column_id") as $K) {
            $U = $K['DATA_TYPE'];
            $te = "$K[DATA_PRECISION],$K[DATA_SCALE]";
            if ($te == ',') {
                $te = $K['CHAR_COL_DECL_LENGTH'];
            }$J[$K['COLUMN_NAME']] = ['field' => $K['COLUMN_NAME'], 'full_type' => $U.($te ? "($te)" : ''), 'type' => strtolower($U), 'length' => $te, 'default' => $K['DATA_DEFAULT'], 'null' => ($K['NULLABLE'] == 'Y'), 'privileges' => ['insert' => 1, 'select' => 1, 'update' => 1, 'where' => 1, 'order' => 1]];
        }

return $J;
    }function indexes($R, $h = null)
    {
        $J = [];
        $Nf = where_owner(' AND ', 'aic.table_owner');
        foreach (get_rows('SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = '.q($R)."$Nf
ORDER BY ac.constraint_type, aic.column_position", $h) as $K) {
            $Ld = $K['INDEX_NAME'];
            $kb = $K['DATA_DEFAULT'];
            $kb = ($kb ? trim($kb, '"') : $K['COLUMN_NAME']);
            $J[$Ld]['type'] = ($K['CONSTRAINT_TYPE'] == 'P' ? 'PRIMARY' : ($K['CONSTRAINT_TYPE'] == 'U' ? 'UNIQUE' : 'INDEX'));
            $J[$Ld]['columns'][] = $kb;
            $J[$Ld]['lengths'][] = ($K['CHAR_LENGTH'] && $K['CHAR_LENGTH'] != $K['COLUMN_LENGTH'] ? $K['CHAR_LENGTH'] : null);
            $J[$Ld]['descs'][] = ($K['DESCEND'] && $K['DESCEND'] == 'DESC' ? '1' : null);
        }

return $J;
    }function view($C)
    {
        $Yi = views_table('view_name, text');
        $L = get_rows('SELECT text "select" FROM '.$Yi.' WHERE view_name = '.q($C));

        return reset($L);
    }function collations()
    {
        return [];
    }function information_schema($k)
    {
        return get_schema() == 'INFORMATION_SCHEMA';
    }function error()
    {
        global $g;

        return h($g->error);
    }function explain($g, $H)
    {
        $g->query("EXPLAIN PLAN FOR $H");

        return $g->query('SELECT * FROM plan_table');
    }function found_rows($S, $Z) {}function auto_increment()
    {
        return '';
    }function alter_table($R, $C, $p, $cd, $nb, $sc, $hb, $Ba, $Wf)
    {
        $c = $cc = [];
        $Gf = ($R ? fields($R) : []);
        foreach ($p as $o) {
            $X = $o[1];
            if ($X && $o[0] != '' && idf_escape($o[0]) != $X[0]) {
                queries('ALTER TABLE '.table($R).' RENAME COLUMN '.idf_escape($o[0])." TO $X[0]");
            }$Ff = $Gf[$o[0]];
            if ($X && $Ff) {
                $kf = process_field($Ff, $Ff);
                if ($X[2] == $kf[2]) {
                    $X[2] = '';
                }
            }if ($X) {
                $c[] = ($R != '' ? ($o[0] != '' ? 'MODIFY (' : 'ADD (') : '  ').implode($X).($R != '' ? ')' : '');
            } else {
                $cc[] = idf_escape($o[0]);
            }
        }if ($R == '') {
            return queries('CREATE TABLE '.table($C)." (\n".implode(",\n", $c)."\n)");
        }

return (! $c || queries('ALTER TABLE '.table($R)."\n".implode("\n", $c))) && (! $cc || queries('ALTER TABLE '.table($R).' DROP ('.implode(', ', $cc).')')) && ($R == $C || queries('ALTER TABLE '.table($R).' RENAME TO '.table($C)));
    }function alter_indexes($R, $c)
    {
        $cc = [];
        $vg = [];
        foreach ($c as $X) {
            if ($X[0] != 'INDEX') {
                $X[2] = preg_replace('~ DESC$~', '', $X[2]);
                $i = ($X[2] == 'DROP' ? "\nDROP CONSTRAINT ".idf_escape($X[1]) : "\nADD".($X[1] != '' ? ' CONSTRAINT '.idf_escape($X[1]) : '')." $X[0] ".($X[0] == 'PRIMARY' ? 'KEY ' : '').'('.implode(', ', $X[2]).')');
                array_unshift($vg, 'ALTER TABLE '.table($R).$i);
            } elseif ($X[2] == 'DROP') {
                $cc[] = idf_escape($X[1]);
            } else {
                $vg[] = 'CREATE INDEX '.idf_escape($X[1] != '' ? $X[1] : uniqid($R.'_')).' ON '.table($R).' ('.implode(', ', $X[2]).')';
            }
        }if ($cc) {
            array_unshift($vg, 'DROP INDEX '.implode(', ', $cc));
        }foreach ($vg as $H) {
            if (! queries($H)) {
                return false;
            }
        }

return true;
    }function foreign_keys($R)
    {
        $J = [];
        $H = "SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = ".q($R);
        foreach (get_rows($H) as $K) {
            $J[$K['NAME']] = ['db' => $K['DEST_DB'], 'table' => $K['DEST_TABLE'], 'source' => [$K['SRC_COLUMN']], 'target' => [$K['DEST_COLUMN']], 'on_delete' => $K['ON_DELETE'], 'on_update' => null];
        }

return $J;
    }function truncate_tables($T)
    {
        return apply_queries('TRUNCATE TABLE', $T);
    }function drop_views($Zi)
    {
        return apply_queries('DROP VIEW', $Zi);
    }function drop_tables($T)
    {
        return apply_queries('DROP TABLE', $T);
    }function last_id()
    {
        return 0;
    }function schemas()
    {
        $J = get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");

        return $J ?: get_vals('SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = '.q(DB).' ORDER BY 1');
    }function get_schema()
    {
        return get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");
    }function set_schema($Xg, $h = null)
    {
        global $g;
        if (! $h) {
            $h = $g;
        }

return $h->query('ALTER SESSION SET CURRENT_SCHEMA = '.idf_escape($Xg));
    }function show_variables()
    {
        return get_key_vals('SELECT name, display_value FROM v$parameter');
    }function process_list()
    {
        return
        get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');
    }function show_status()
    {
        $L = get_rows('SELECT * FROM v$instance');

        return reset($L);
    }function convert_field($o) {}function unconvert_field($o, $J)
    {
        return $J;
    }function support($Pc)
    {
        return preg_match('~^(columns|database|drop_col|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~', $Pc);
    }
}$bc['mssql'] = 'MS SQL';
if (isset($_GET['mssql'])) {
    define('Adminer\DRIVER', 'mssql');
    if (extension_loaded('sqlsrv')) {
        class Db
        {
            public $extension = 'sqlsrv';

            public $server_infovar;

            public $affected_rowsvar;

            public $errnovar;

            public $errorvar;

            private $link;

            public $resultprivate;

            private function get_error()
            {
                $this->error = '';
                foreach (sqlsrv_errors() as $n) {
                    $this->errno = $n['code'];
                    $this->error .= "$n[message]\n";
                }$this->error = rtrim($this->error);
            }

            public function connect($N, $V, $F)
            {
                global $b;
                $tb = ['UID' => $V, 'PWD' => $F, 'CharacterSet' => 'UTF-8'];
                $Bh = $b->connectSsl();
                if (isset($Bh['Encrypt'])) {
                    $tb['Encrypt'] = $Bh['Encrypt'];
                }if (isset($Bh['TrustServerCertificate'])) {
                    $tb['TrustServerCertificate'] = $Bh['TrustServerCertificate'];
                }$k = $b->database();
                if ($k != '') {
                    $tb['Database'] = $k;
                }$this->link = @sqlsrv_connect(preg_replace('~:~', ',', $N), $tb);
                if ($this->link) {
                    $Pd = sqlsrv_server_info($this->link);
                    $this->server_info = $Pd['SQLServerVersion'];
                } else {
                    $this->get_error();
                }

return (bool) $this->link;
            }

            public function quote($Q)
            {
                $Ci = strlen($Q) != strlen(utf8_decode($Q));

                return ($Ci ? 'N' : '')."'".str_replace("'", "''", $Q)."'";
            }

            public function select_db($Kb)
            {
                return $this->query(use_sql($Kb));
            }

            public function query($H, $Bi = false)
            {
                $I = sqlsrv_query($this->link, $H);
                $this->error = '';
                if (! $I) {
                    $this->get_error();

                    return false;
                }

return $this->store_result($I);
            }

            public function multi_query($H)
            {
                $this->result = sqlsrv_query($this->link, $H);
                $this->error = '';
                if (! $this->result) {
                    $this->get_error();

                    return false;
                }

return true;
            }

            public function store_result($I = null)
            {
                if (! $I) {
                    $I = $this->result;
                }if (! $I) {
                    return false;
                }if (sqlsrv_field_metadata($I)) {
                    return new Result($I);
                }$this->affected_rows = sqlsrv_rows_affected($I);

                return true;
            }

            public function next_result()
            {
                return $this->result ? sqlsrv_next_result($this->result) : null;
            }

            public function result($H, $o = 0)
            {
                $I = $this->query($H);
                if (! is_object($I)) {
                    return false;
                }$K = $I->fetch_row();

                return $K[$o];
            }
        }class Result
        {
            public $num_rows;

            private $result;

            public $offsetprivate = 0;

            public $fieldsprivate;

            public function __construct($I)
            {
                $this->result = $I;
            }

            private function convert($K)
            {
                foreach ((array) $K as $z => $X) {
                    if (is_a($X, 'DateTime')) {
                        $K[$z] = $X->format('Y-m-d H:i:s');
                    }
                }

return $K;
            }

            public function fetch_assoc()
            {
                return $this->convert(sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC));
            }

            public function fetch_row()
            {
                return $this->convert(sqlsrv_fetch_array($this->result, SQLSRV_FETCH_NUMERIC));
            }

            public function fetch_field()
            {
                if (! $this->fields) {
                    $this->fields = sqlsrv_field_metadata($this->result);
                }$o = $this->fields[$this->offset++];
                $J = new \stdClass;
                $J->name = $o['Name'];
                $J->orgname = $o['Name'];
                $J->type = ($o['Type'] == 1 ? 254 : 0);

                return $J;
            }

            public function seek($D)
            {
                for ($u = 0; $u < $D; $u++) {
                    sqlsrv_fetch($this->result);
                }
            }

            public function __destruct()
            {
                sqlsrv_free_stmt($this->result);
            }
        }
    } elseif (extension_loaded('pdo_sqlsrv')) {
        class Db extends PdoDb
        {
            public $extension = 'PDO_SQLSRV';

            public function connect($N, $V, $F)
            {
                $this->dsn('sqlsrv:Server='.str_replace(':', ',', $N), $V, $F);

                return true;
            }

            public function select_db($Kb)
            {
                return $this->query(use_sql($Kb));
            }
        }
    } elseif (extension_loaded('pdo_dblib')) {
        class Db extends PdoDb
        {
            public $extension = 'PDO_DBLIB';

            public function connect($N, $V, $F)
            {
                $this->dsn('dblib:charset=utf8;host='.str_replace(':', ';unix_socket=', preg_replace('~:(\d)~', ';port=\1', $N)), $V, $F);

                return true;
            }

            public function select_db($Kb)
            {
                return $this->query(use_sql($Kb));
            }
        }
    }class Driver extends SqlDriver
    {
        public static $ig = ['SQLSRV', 'PDO_SQLSRV', 'PDO_DBLIB'];

        public static $de = 'mssql';

        public $editFunctions = [['date|time' => 'getdate'], ['int|decimal|real|float|money|datetime' => '+/-', 'char|text' => '+']];

        public $operators = ['=', '<', '>', '<=', '>=', '!=', 'LIKE', 'LIKE %%', 'IN', 'IS NULL', 'NOT LIKE', 'NOT IN', 'IS NOT NULL'];

        public $functions = ['len', 'lower', 'round', 'upper'];

        public $grouping = ['avg', 'count', 'count distinct', 'max', 'min', 'sum'];

        public $onActions = 'NO ACTION|CASCADE|SET NULL|SET DEFAULT';

        public $generated = ['PERSISTED', 'VIRTUAL'];

        public function __construct($g)
        {
            parent::__construct($g);
            $this->types = [lang(25) => ['tinyint' => 3, 'smallint' => 5, 'int' => 10, 'bigint' => 20, 'bit' => 1, 'decimal' => 0, 'real' => 12, 'float' => 53, 'smallmoney' => 10, 'money' => 20], lang(26) => ['date' => 10, 'smalldatetime' => 19, 'datetime' => 19, 'datetime2' => 19, 'time' => 8, 'datetimeoffset' => 10], lang(27) => ['char' => 8000, 'varchar' => 8000, 'text' => 2147483647, 'nchar' => 4000, 'nvarchar' => 4000, 'ntext' => 1073741823], lang(28) => ['binary' => 8000, 'varbinary' => 8000, 'image' => 2147483647]];
        }

        public function insertUpdate($R, $L, $G)
        {
            $p = fields($R);
            $Ji = [];
            $Z = [];
            $O = reset($L);
            $e = 'c'.implode(', c', range(1, count($O)));
            $Pa = 0;
            $Td = [];
            foreach ($O as $z => $X) {
                $Pa++;
                $C = idf_unescape($z);
                if (! $p[$C]['auto_increment']) {
                    $Td[$z] = "c$Pa";
                }if (isset($G[$C])) {
                    $Z[] = "$z = c$Pa";
                } else {
                    $Ji[] = "$z = c$Pa";
                }
            }$Ui = [];
            foreach ($L as $O) {
                $Ui[] = '('.implode(', ', $O).')';
            }if ($Z) {
                $Fd = queries('SET IDENTITY_INSERT '.table($R).' ON');
                $J = queries('MERGE '.table($R)." USING (VALUES\n\t".implode(",\n\t", $Ui)."\n) AS source ($e) ON ".implode(' AND ', $Z).($Ji ? "\nWHEN MATCHED THEN UPDATE SET ".implode(', ', $Ji) : '')."\nWHEN NOT MATCHED THEN INSERT (".implode(', ', array_keys($Fd ? $O : $Td)).') VALUES ('.($Fd ? $e : implode(', ', $Td)).');');
                if ($Fd) {
                    queries('SET IDENTITY_INSERT '.table($R).' OFF');
                }
            } else {
                $J = queries('INSERT INTO '.table($R).' ('.implode(', ', array_keys($O)).") VALUES\n".implode(",\n", $Ui));
            }

return $J;
        }

        public function begin()
        {
            return queries('BEGIN TRANSACTION');
        }

        public function tableHelp($C, $be = false)
        {
            $we = ['sys' => 'catalog-views/sys-', 'INFORMATION_SCHEMA' => 'information-schema-views/'];
            $A = $we[get_schema()];
            if ($A) {
                return "relational-databases/system-$A".preg_replace('~_~', '-', strtolower($C)).'-transact-sql';
            }
        }
    }function idf_escape($w)
    {
        return '['.str_replace(']', ']]', $w).']';
    }function table($w)
    {
        return ($_GET['ns'] != '' ? idf_escape($_GET['ns']).'.' : '').idf_escape($w);
    }function connect($Cb)
    {
        $g = new Db;
        if ($Cb[0] == '') {
            $Cb[0] = 'localhost:1433';
        }if ($g->connect($Cb[0], $Cb[1], $Cb[2])) {
            return $g;
        }

return $g->error;
    }function get_databases()
    {
        return get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");
    }function limit($H, $Z, $_, $D = 0, $fh = ' ')
    {
        return ($_ !== null ? ' TOP ('.($_ + $D).')' : '')." $H$Z";
    }function limit1($R, $H, $Z, $fh = "\n")
    {
        return limit($H, $Z, 1, 0, $fh);
    }function db_collation($k, $ib)
    {
        return get_val('SELECT collation_name FROM sys.databases WHERE name = '.q($k));
    }function engines()
    {
        return [];
    }function logged_user()
    {
        return get_val('SELECT SUSER_NAME()');
    }function tables_list()
    {
        return get_key_vals('SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID('.q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");
    }function count_tables($j)
    {
        global $g;
        $J = [];
        foreach ($j as $k) {
            $g->select_db($k);
            $J[$k] = get_val('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES');
        }

return $J;
    }function table_status($C = '')
    {
        $J = [];
        foreach (get_rows("SELECT ao.name AS Name, ao.type_desc AS Engine, (SELECT value FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($C != '' ? 'AND name = '.q($C) : 'ORDER BY name')) as $K) {
            if ($C != '') {
                return $K;
            }$J[$K['Name']] = $K;
        }

return $J;
    }function is_view($S)
    {
        return $S['Engine'] == 'VIEW';
    }function fk_support($S)
    {
        return true;
    }function fields($R)
    {
        $pb = get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($R).", 'column', NULL)");
        $J = [];
        $Nh = get_val('SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID('.q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($R));
        foreach (get_rows('SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name, t.name type, CAST(d.definition as text) [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = '.q($Nh)) as $K) {
            $U = $K['type'];
            $te = (preg_match('~char|binary~', $U) ? $K['max_length'] / ($U[0] == 'n' ? 2 : 1) : ($U == 'decimal' ? "$K[precision],$K[scale]" : ''));
            $J[$K['name']] = ['field' => $K['name'], 'full_type' => $U.($te ? "($te)" : ''), 'type' => $U, 'length' => $te, 'default' => (preg_match("~^\('(.*)'\)$~", $K['default'], $B) ? str_replace("''", "'", $B[1]) : $K['default']), 'default_constraint' => $K['default_constraint'], 'null' => $K['is_nullable'], 'auto_increment' => $K['is_identity'], 'collation' => $K['collation_name'], 'privileges' => ['insert' => 1, 'select' => 1, 'update' => 1, 'where' => 1, 'order' => 1], 'primary' => $K['is_primary_key'], 'comment' => $pb[$K['name']]];
        }foreach (get_rows('SELECT * FROM sys.computed_columns WHERE object_id = '.q($Nh)) as $K) {
            $J[$K['name']]['generated'] = ($K['is_persisted'] ? 'PERSISTED' : 'VIRTUAL');
            $J[$K['name']]['default'] = $K['definition'];
        }

return $J;
    }function indexes($R, $h = null)
    {
        $J = [];
        foreach (get_rows('SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = '.q($R), $h) as $K) {
            $C = $K['name'];
            $J[$C]['type'] = ($K['is_primary_key'] ? 'PRIMARY' : ($K['is_unique'] ? 'UNIQUE' : 'INDEX'));
            $J[$C]['lengths'] = [];
            $J[$C]['columns'][$K['key_ordinal']] = $K['column_name'];
            $J[$C]['descs'][$K['key_ordinal']] = ($K['is_descending_key'] ? '1' : null);
        }

return $J;
    }function view($C)
    {
        return ['select' => preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU', '', get_val('SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = '.q($C)))];
    }function collations()
    {
        $J = [];
        foreach (get_vals('SELECT name FROM fn_helpcollations()') as $hb) {
            $J[preg_replace('~_.*~', '', $hb)][] = $hb;
        }

return $J;
    }function information_schema($k)
    {
        return get_schema() == 'INFORMATION_SCHEMA';
    }function error()
    {
        global $g;

        return nl_br(h(preg_replace('~^(\[[^]]*])+~m', '', $g->error)));
    }function create_database($k, $hb)
    {
        return queries('CREATE DATABASE '.idf_escape($k).(preg_match('~^[a-z0-9_]+$~i', $hb) ? " COLLATE $hb" : ''));
    }function drop_databases($j)
    {
        return queries('DROP DATABASE '.implode(', ', array_map('Adminer\idf_escape', $j)));
    }function rename_database($C, $hb)
    {
        if (preg_match('~^[a-z0-9_]+$~i', $hb)) {
            queries('ALTER DATABASE '.idf_escape(DB)." COLLATE $hb");
        }queries('ALTER DATABASE '.idf_escape(DB).' MODIFY NAME = '.idf_escape($C));

        return true;
    }function auto_increment()
    {
        return ' IDENTITY'.($_POST['Auto_increment'] != '' ? '('.number($_POST['Auto_increment']).',1)' : '').' PRIMARY KEY';
    }function alter_table($R, $C, $p, $cd, $nb, $sc, $hb, $Ba, $Wf)
    {
        $c = [];
        $pb = [];
        $Gf = fields($R);
        foreach ($p as $o) {
            $d = idf_escape($o[0]);
            $X = $o[1];
            if (! $X) {
                $c['DROP'][] = " COLUMN $d";
            } else {
                $X[1] = preg_replace("~( COLLATE )'(\\w+)'~", '\1\2', $X[1]);
                $pb[$o[0]] = $X[5];
                unset($X[5]);
                if (preg_match('~ AS ~', $X[3])) {
                    unset($X[1],$X[2]);
                }if ($o[0] == '') {
                    $c['ADD'][] = "\n  ".implode('', $X).($R == '' ? substr($cd[$X[0]], 16 + strlen($X[0])) : '');
                } else {
                    $l = $X[3];
                    unset($X[3]);
                    unset($X[6]);
                    if ($d != $X[0]) {
                        queries('EXEC sp_rename '.q(table($R).".$d").', '.q(idf_unescape($X[0])).", 'COLUMN'");
                    }$c['ALTER COLUMN '.implode('', $X)][] = '';
                    $Ff = $Gf[$o[0]];
                    if (default_value($Ff) != $l) {
                        if ($Ff['default'] !== null) {
                            $c['DROP'][] = ' '.idf_escape($Ff['default_constraint']);
                        }if ($l) {
                            $c['ADD'][] = "\n $l FOR $d";
                        }
                    }
                }
            }
        }if ($R == '') {
            return queries('CREATE TABLE '.table($C).' ('.implode(',', (array) $c['ADD'])."\n)");
        }if ($R != $C) {
            queries('EXEC sp_rename '.q(table($R)).', '.q($C));
        }if ($cd) {
            $c[''] = $cd;
        }foreach ($c as $z => $X) {
            if (! queries('ALTER TABLE '.table($C)." $z".implode(',', $X))) {
                return false;
            }
        }foreach ($pb as $z => $X) {
            $nb = substr($X, 9);
            queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($C).", @level2type = N'Column', @level2name = ".q($z));
            queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $nb,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($C).",
@level2type = N'Column',
@level2name = ".q($z));
        }

return true;
    }function alter_indexes($R, $c)
    {
        $x = [];
        $cc = [];
        foreach ($c as $X) {
            if ($X[2] == 'DROP') {
                if ($X[0] == 'PRIMARY') {
                    $cc[] = idf_escape($X[1]);
                } else {
                    $x[] = idf_escape($X[1]).' ON '.table($R);
                }
            } elseif (! queries(($X[0] != 'PRIMARY' ? "CREATE $X[0] ".($X[0] != 'INDEX' ? 'INDEX ' : '').idf_escape($X[1] != '' ? $X[1] : uniqid($R.'_')).' ON '.table($R) : 'ALTER TABLE '.table($R).' ADD PRIMARY KEY').' ('.implode(', ', $X[2]).')')) {
                return false;
            }
        }

return (! $x || queries('DROP INDEX '.implode(', ', $x))) && (! $cc || queries('ALTER TABLE '.table($R).' DROP '.implode(', ', $cc)));
    }function last_id()
    {
        return get_val('SELECT SCOPE_IDENTITY()');
    }function explain($g, $H)
    {
        $g->query('SET SHOWPLAN_ALL ON');
        $J = $g->query($H);
        $g->query('SET SHOWPLAN_ALL OFF');

        return $J;
    }function found_rows($S, $Z) {}function foreign_keys($R)
    {
        $J = [];
        $rf = ['CASCADE', 'NO ACTION', 'SET NULL', 'SET DEFAULT'];
        foreach (get_rows('EXEC sp_fkeys @fktable_name = '.q($R).', @fktable_owner = '.q(get_schema())) as $K) {
            $r = &$J[$K['FK_NAME']];
            $r['db'] = $K['PKTABLE_QUALIFIER'];
            $r['ns'] = $K['PKTABLE_OWNER'];
            $r['table'] = $K['PKTABLE_NAME'];
            $r['on_update'] = $rf[$K['UPDATE_RULE']];
            $r['on_delete'] = $rf[$K['DELETE_RULE']];
            $r['source'][] = $K['FKCOLUMN_NAME'];
            $r['target'][] = $K['PKCOLUMN_NAME'];
        }

return $J;
    }function truncate_tables($T)
    {
        return apply_queries('TRUNCATE TABLE', $T);
    }function drop_views($Zi)
    {
        return queries('DROP VIEW '.implode(', ', array_map('Adminer\table', $Zi)));
    }function drop_tables($T)
    {
        return queries('DROP TABLE '.implode(', ', array_map('Adminer\table', $T)));
    }function move_tables($T, $Zi, $Wh)
    {
        return apply_queries('ALTER SCHEMA '.idf_escape($Wh).' TRANSFER', array_merge($T, $Zi));
    }function trigger($C)
    {
        if ($C == '') {
            return [];
        }$L = get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($C));
        $J = reset($L);
        if ($J) {
            $J['Statement'] = preg_replace('~^.+\s+AS\s+~isU', '', $J['text']);
        }

return $J;
    }function triggers($R)
    {
        $J = [];
        foreach (get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($R)) as $K) {
            $J[$K['name']] = [$K['Timing'], $K['Event']];
        }

return $J;
    }function trigger_options()
    {
        return ['Timing' => ['AFTER', 'INSTEAD OF'], 'Event' => ['INSERT', 'UPDATE', 'DELETE'], 'Type' => ['AS']];
    }function schemas()
    {
        return get_vals('SELECT name FROM sys.schemas');
    }function get_schema()
    {
        if ($_GET['ns'] != '') {
            return $_GET['ns'];
        }

return get_val('SELECT SCHEMA_NAME()');
    }function set_schema($Vg)
    {
        $_GET['ns'] = $Vg;

        return true;
    }function create_sql($R, $Ba, $Gh)
    {
        global $m;
        if (is_view(table_status($R))) {
            $Yi = view($R);

            return 'CREATE VIEW '.table($R)." AS $Yi[select]";
        }$p = [];
        $G = false;
        foreach (fields($R) as $C => $o) {
            $X = process_field($o, $o);
            if ($X[6]) {
                $G = true;
            }$p[] = implode('', $X);
        }foreach (indexes($R) as $C => $x) {
            if (! $G || $x['type'] != 'PRIMARY') {
                $e = [];
                foreach ($x['columns'] as $z => $X) {
                    $e[] = idf_escape($X).($x['descs'][$z] ? ' DESC' : '');
                }$C = idf_escape($C);
                $p[] = ($x['type'] == 'INDEX' ? "INDEX $C" : "CONSTRAINT $C ".($x['type'] == 'UNIQUE' ? 'UNIQUE' : 'PRIMARY KEY')).' ('.implode(', ', $e).')';
            }
        }foreach ($m->checkConstraints($R) as $C => $Va) {
            $p[] = 'CONSTRAINT '.idf_escape($C)." CHECK ($Va)";
        }

return 'CREATE TABLE '.table($R)." (\n\t".implode(",\n\t", $p)."\n)";
    }function foreign_keys_sql($R)
    {
        $p = [];
        foreach (foreign_keys($R) as $cd) {
            $p[] = ltrim(format_foreign_key($cd));
        }

return $p ? 'ALTER TABLE '.table($R)." ADD\n\t".implode(",\n\t", $p).";\n\n" : '';
    }function truncate_sql($R)
    {
        return 'TRUNCATE TABLE '.table($R);
    }function use_sql($Kb)
    {
        return 'USE '.idf_escape($Kb);
    }function trigger_sql($R)
    {
        $J = '';
        foreach (triggers($R) as $C => $vi) {
            $J .= create_trigger(' ON '.table($R), trigger($C)).';';
        }

return $J;
    }function convert_field($o) {}function unconvert_field($o, $J)
    {
        return $J;
    }function support($Pc)
    {
        return preg_match('~^(check|comment|columns|database|drop_col|dump|indexes|descidx|scheme|sql|table|trigger|view|view_trigger)$~', $Pc);
    }
}class Adminer
{
    public $operators;

    public function name()
    {
        return "<a href='https://www.adminer.org/'".target_blank()." id='h1'>Adminer</a>";
    }

    public function credentials()
    {
        return [SERVER, $_GET['username'], get_password()];
    }

    public function connectSsl() {}

    public function permanentLogin($i = false)
    {
        return password_file($i);
    }

    public function bruteForceKey()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function serverName($N)
    {
        return h($N);
    }

    public function database()
    {
        return DB;
    }

    public function databases($ad = true)
    {
        return get_databases($ad);
    }

    public function schemas()
    {
        return schemas();
    }

    public function queryTimeout()
    {
        return 2;
    }

    public function headers() {}

    public function csp()
    {
        return csp();
    }

    public function head($Hb = null)
    {
        return true;
    }

    public function css()
    {
        $J = [];
        foreach (['', '-dark'] as $Te) {
            $q = "adminer$Te.css";
            if (file_exists($q)) {
                $J[] = "$q?v=".crc32(file_get_contents($q));
            }
        }

return $J;
    }

    public function loginForm()
    {
        global $bc;
        echo "<table class='layout'>\n",$this->loginFormField('driver', '<tr><th>'.lang(32).'<td>', html_select('auth[driver]', $bc, DRIVER, 'loginDriver(this);')),$this->loginFormField('server', '<tr><th>'.lang(33).'<td>', '<input name="auth[server]" value="'.h(SERVER).'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'),$this->loginFormField('username', '<tr><th>'.lang(34).'<td>', '<input name="auth[username]" id="username" autofocus value="'.h($_GET['username']).'" autocomplete="username" autocapitalize="off">'.script("qs('#username').form['auth[driver]'].onchange();")),$this->loginFormField('password', '<tr><th>'.lang(35).'<td>', '<input type="password" name="auth[password]" autocomplete="current-password">'),$this->loginFormField('db', '<tr><th>'.lang(36).'<td>', '<input name="auth[db]" value="'.h($_GET['db']).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(37)."'>\n",checkbox('auth[permanent]', 1, $_COOKIE['adminer_permanent'], lang(38))."\n";
    }

    public function loginFormField($C, $_d, $Y)
    {
        return $_d.$Y."\n";
    }

    public function login($ye, $F)
    {
        if ($F == '') {
            return lang(39, target_blank());
        }

return true;
    }

    public function tableName($Mh)
    {
        return h($Mh['Name']);
    }

    public function fieldName($o, $_f = 0)
    {
        return '<span title="'.h($o['full_type'].($o['comment'] != '' ? " : $o[comment]" : '')).'">'.h($o['field']).'</span>';
    }

    public function selectLinks($Mh, $O = '')
    {
        global $m;
        echo '<p class="links">';
        $we = ['select' => lang(40)];
        if (support('table') || support('indexes')) {
            $we['table'] = lang(41);
        }$be = false;
        if (support('table')) {
            $be = is_view($Mh);
            if ($be) {
                $we['view'] = lang(42);
            } else {
                $we['create'] = lang(43);
            }
        }if ($O !== null) {
            $we['edit'] = lang(44);
        }$C = $Mh['Name'];
        foreach ($we as $z => $X) {
            echo " <a href='".h(ME)."$z=".urlencode($C).($z == 'edit' ? $O : '')."'".bold(isset($_GET[$z])).">$X</a>";
        }echo doc_link([JUSH => $m->tableHelp($C, $be)], '?'),"\n";
    }

    public function foreignKeys($R)
    {
        return foreign_keys($R);
    }

    public function backwardKeys($R, $Lh)
    {
        return [];
    }

    public function backwardKeysPrint($Fa, $K) {}

    public function selectQuery($H, $Ch, $Nc = false)
    {
        global $m;
        $J = "</p>\n";
        if (! $Nc && ($cj = $m->warnings())) {
            $v = 'warnings';
            $J = ", <a href='#$v'>".lang(45).'</a>'.script("qsl('a').onclick = partial(toggle, '$v');", '')."$J<div id='$v' class='hidden'>\n$cj</div>\n";
        }

return "<p><code class='jush-".JUSH."'>".h(str_replace("\n", ' ', $H))."</code> <span class='time'>(".format_time($Ch).')</span>'.(support('sql') ? " <a href='".h(ME).'sql='.urlencode($H)."'>".lang(10).'</a>' : '').$J;
    }

    public function sqlCommandQuery($H)
    {
        return shorten_utf8(trim($H), 1000);
    }

    public function rowDescription($R)
    {
        return '';
    }

    public function rowDescriptions($L, $dd)
    {
        return $L;
    }

    public function selectLink($X, $o) {}

    public function selectVal($X, $A, $o, $Jf)
    {
        $J = ($X === null ? '<i>NULL</i>' : (preg_match('~char|binary|boolean~', $o['type']) && ! preg_match('~var~', $o['type']) ? "<code>$X</code>" : (preg_match('~json~', $o['type']) ? "<code class='jush-js'>$X</code>" : $X)));
        if (preg_match('~blob|bytea|raw|file~', $o['type']) && ! is_utf8($X)) {
            $J = '<i>'.lang(46, strlen($Jf)).'</i>';
        }

return $A ? "<a href='".h($A)."'".(is_url($A) ? target_blank() : '').">$J</a>" : $J;
    }

    public function editVal($X, $o)
    {
        return $X;
    }

    public function tableStructurePrint($p)
    {
        global $m;
        echo "<div class='scrollable'>\n","<table class='nowrap odds'>\n",'<thead><tr><th>'.lang(47).'<td>'.lang(48).(support('comment') ? '<td>'.lang(49) : '')."</thead>\n";
        $Fh = $m->structuredTypes();
        foreach ($p as $o) {
            echo '<tr><th>'.h($o['field']);
            $U = h($o['full_type']);
            echo "<td><span title='".h($o['collation'])."'>".(in_array($U, (array) $Fh[lang(31)]) ? "<a href='".h(ME.'type='.urlencode($U))."'>$U</a>" : $U).'</span>',($o['null'] ? ' <i>NULL</i>' : ''),($o['auto_increment'] ? ' <i>'.lang(50).'</i>' : '');
            $l = h($o['default']);
            echo (isset($o['default']) ? " <span title='".lang(51)."'>[<b>".($o['generated'] ? "<code class='jush-".JUSH."'>$l</code>" : $l).'</b>]</span>' : ''),(support('comment') ? '<td>'.h($o['comment']) : ''),"\n";
        }echo "</table>\n","</div>\n";
    }

    public function tableIndexesPrint($y)
    {
        echo "<table>\n";
        foreach ($y as $C => $x) {
            ksort($x['columns']);
            $ng = [];
            foreach ($x['columns'] as $z => $X) {
                $ng[] = '<i>'.h($X).'</i>'.($x['lengths'][$z] ? '('.$x['lengths'][$z].')' : '').($x['descs'][$z] ? ' DESC' : '');
            }echo "<tr title='".h($C)."'><th>$x[type]<td>".implode(', ', $ng)."\n";
        }echo "</table>\n";
    }

    public function selectColumnsPrint($M, $e)
    {
        global $m;
        print_fieldset('select', lang(52), $M);
        $u = 0;
        $M[''] = [];
        foreach ($M as $z => $X) {
            $X = $_GET['columns'][$z];
            $d = select_input(" name='columns[$u][col]'", $e, $X['col'], ($z !== '' ? 'selectFieldChange' : 'selectAddRow'));
            echo '<div>'.($m->functions || $m->grouping ? html_select("columns[$u][fun]", [-1 => ''] + array_filter([lang(53) => $m->functions, lang(54) => $m->grouping]), $X['fun']).on_help("getTarget(event).value && getTarget(event).value.replace(/ |\$/, '(') + ')'", 1).script("qsl('select').onchange = function () { helpClose();".($z !== '' ? '' : " qsl('select, input', this.parentNode).onchange();").' };', '')."($d)" : $d)."</div>\n";
            $u++;
        }echo "</div></fieldset>\n";
    }

    public function selectSearchPrint($Z, $e, $y)
    {
        print_fieldset('search', lang(55), $Z);
        foreach ($y as $u => $x) {
            if ($x['type'] == 'FULLTEXT') {
                echo '<div>(<i>'.implode('</i>, <i>', array_map('Adminer\h', $x['columns'])).'</i>) AGAINST'," <input type='search' name='fulltext[$u]' value='".h($_GET['fulltext'][$u])."'>",script("qsl('input').oninput = selectFieldChange;", ''),checkbox("boolean[$u]", 1, isset($_GET['boolean'][$u]), 'BOOL'),"</div>\n";
            }
        }$Ta = 'this.parentNode.firstChild.onchange();';
        foreach (array_merge((array) $_GET['where'], [[]]) as $u => $X) {
            if (! $X || ("$X[col]$X[val]" != '' && in_array($X['op'], $this->operators))) {
                echo '<div>'.select_input(" name='where[$u][col]'", $e, $X['col'], ($X ? 'selectFieldChange' : 'selectAddRow'), '('.lang(56).')'),html_select("where[$u][op]", $this->operators, $X['op'], $Ta),"<input type='search' name='where[$u][val]' value='".h($X['val'])."'>",script("mixin(qsl('input'), {oninput: function () { $Ta }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});", ''),"</div>\n";
            }
        }echo "</div></fieldset>\n";
    }

    public function selectOrderPrint($_f, $e, $y)
    {
        print_fieldset('sort', lang(57), $_f);
        $u = 0;
        foreach ((array) $_GET['order'] as $z => $X) {
            if ($X != '') {
                echo '<div>'.select_input(" name='order[$u]'", $e, $X, 'selectFieldChange'),checkbox("desc[$u]", 1, isset($_GET['desc'][$z]), lang(58))."</div>\n";
                $u++;
            }
        }echo '<div>'.select_input(" name='order[$u]'", $e, '', 'selectAddRow'),checkbox("desc[$u]", 1, false, lang(58))."</div>\n","</div></fieldset>\n";
    }

    public function selectLimitPrint($_)
    {
        echo '<fieldset><legend>'.lang(59).'</legend><div>',"<input type='number' name='limit' class='size' value='".h($_)."'>",script("qsl('input').oninput = selectFieldChange;", ''),"</div></fieldset>\n";
    }

    public function selectLengthPrint($ci)
    {
        if ($ci !== null) {
            echo '<fieldset><legend>'.lang(60).'</legend><div>',"<input type='number' name='text_length' class='size' value='".h($ci)."'>","</div></fieldset>\n";
        }
    }

    public function selectActionPrint($y)
    {
        echo '<fieldset><legend>'.lang(61).'</legend><div>',"<input type='submit' value='".lang(52)."'>"," <span id='noindex' title='".lang(62)."'></span>",'<script'.nonce().">\n",'var indexColumns = ';
        $e = [];
        foreach ($y as $x) {
            $Gb = reset($x['columns']);
            if ($x['type'] != 'FULLTEXT' && $Gb) {
                $e[$Gb] = 1;
            }
        }$e[''] = 1;
        foreach ($e as $z => $X) {
            json_row($z);
        }echo ";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";
    }

    public function selectCommandPrint()
    {
        return ! information_schema(DB);
    }

    public function selectImportPrint()
    {
        return ! information_schema(DB);
    }

    public function selectEmailPrint($pc, $e) {}

    public function selectColumnsProcess($e, $y)
    {
        global $m;
        $M = [];
        $pd = [];
        foreach ((array) $_GET['columns'] as $z => $X) {
            if ($X['fun'] == 'count' || ($X['col'] != '' && (! $X['fun'] || in_array($X['fun'], $m->functions) || in_array($X['fun'], $m->grouping)))) {
                $M[$z] = apply_sql_function($X['fun'], ($X['col'] != '' ? idf_escape($X['col']) : '*'));
                if (! in_array($X['fun'], $m->grouping)) {
                    $pd[] = $M[$z];
                }
            }
        }

return [$M, $pd];
    }

    public function selectSearchProcess($p, $y)
    {
        global $g,$m;
        $J = [];
        foreach ($y as $u => $x) {
            if ($x['type'] == 'FULLTEXT' && $_GET['fulltext'][$u] != '') {
                $J[] = 'MATCH ('.implode(', ', array_map('Adminer\idf_escape', $x['columns'])).') AGAINST ('.q($_GET['fulltext'][$u]).(isset($_GET['boolean'][$u]) ? ' IN BOOLEAN MODE' : '').')';
            }
        }foreach ((array) $_GET['where'] as $z => $X) {
            if ("$X[col]$X[val]" != '' && in_array($X['op'], $this->operators)) {
                $kg = '';
                $qb = " $X[op]";
                if (preg_match('~IN$~', $X['op'])) {
                    $Jd = process_length($X['val']);
                    $qb .= ' '.($Jd != '' ? $Jd : '(NULL)');
                } elseif ($X['op'] == 'SQL') {
                    $qb = " $X[val]";
                } elseif ($X['op'] == 'LIKE %%') {
                    $qb = ' LIKE '.$this->processInput($p[$X['col']], "%$X[val]%");
                } elseif ($X['op'] == 'ILIKE %%') {
                    $qb = ' ILIKE '.$this->processInput($p[$X['col']], "%$X[val]%");
                } elseif ($X['op'] == 'FIND_IN_SET') {
                    $kg = "$X[op](".q($X['val']).', ';
                    $qb = ')';
                } elseif (! preg_match('~NULL$~', $X['op'])) {
                    $qb .= ' '.$this->processInput($p[$X['col']], $X['val']);
                }if ($X['col'] != '') {
                    $J[] = $kg.$m->convertSearch(idf_escape($X['col']), $X, $p[$X['col']]).$qb;
                } else {
                    $jb = [];
                    foreach ($p as $C => $o) {
                        if (isset($o['privileges']['where']) && (preg_match('~^[-\d.'.(preg_match('~IN$~', $X['op']) ? ',' : '').']+$~', $X['val']) || ! preg_match('~'.number_type().'|bit~', $o['type'])) && (! preg_match("~[\x80-\xFF]~", $X['val']) || preg_match('~char|text|enum|set~', $o['type'])) && (! preg_match('~date|timestamp~', $o['type']) || preg_match('~^\d+-\d+-\d+~', $X['val']))) {
                            $jb[] = $kg.$m->convertSearch(idf_escape($C), $X, $o).$qb;
                        }
                    }$J[] = ($jb ? '('.implode(' OR ', $jb).')' : '1 = 0');
                }
            }
        }

return $J;
    }

    public function selectOrderProcess($p, $y)
    {
        $J = [];
        foreach ((array) $_GET['order'] as $z => $X) {
            if ($X != '') {
                $J[] = (preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~', $X) ? $X : idf_escape($X)).(isset($_GET['desc'][$z]) ? ' DESC' : '');
            }
        }

return $J;
    }

    public function selectLimitProcess()
    {
        return isset($_GET['limit']) ? $_GET['limit'] : '50';
    }

    public function selectLengthProcess()
    {
        return isset($_GET['text_length']) ? $_GET['text_length'] : '100';
    }

    public function selectEmailProcess($Z, $dd)
    {
        return false;
    }

    public function selectQueryBuild($M, $Z, $pd, $_f, $_, $E)
    {
        return '';
    }

    public function messageQuery($H, $di, $Nc = false)
    {
        global $m;
        restart_session();
        $Ad = &get_session('queries');
        if (! $Ad[$_GET['db']]) {
            $Ad[$_GET['db']] = [];
        }if (strlen($H) > 1e6) {
            $H = preg_replace('~[\x80-\xFF]+$~', '', substr($H, 0, 1e6))."\n…";
        }$Ad[$_GET['db']][] = [$H, time(), $di];
        $zh = 'sql-'.count($Ad[$_GET['db']]);
        $J = "<a href='#$zh' class='toggle'>".lang(63)."</a>\n";
        if (! $Nc && ($cj = $m->warnings())) {
            $v = 'warnings-'.count($Ad[$_GET['db']]);
            $J = "<a href='#$v' class='toggle'>".lang(45)."</a>, $J<div id='$v' class='hidden'>\n$cj</div>\n";
        }

return " <span class='time'>".@date('H:i:s').'</span>'." $J<div id='$zh' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H, 1000).'</code></pre>'.($di ? " <span class='time'>($di)</span>" : '').(support('sql') ? '<p><a href="'.h(str_replace('db='.urlencode(DB), 'db='.urlencode($_GET['db']), ME).'sql=&history='.(count($Ad[$_GET['db']]) - 1)).'">'.lang(10).'</a>' : '').'</div>';
    }

    public function editRowPrint($R, $p, $K, $Ji) {}

    public function editFunctions($o)
    {
        global $m;
        $J = ($o['null'] ? 'NULL/' : '');
        $Ji = isset($_GET['select']) || where($_GET);
        foreach ($m->editFunctions as $z => $kd) {
            if (! $z || (! isset($_GET['call']) && $Ji)) {
                foreach ($kd as $ag => $X) {
                    if (! $ag || preg_match("~$ag~", $o['type'])) {
                        $J .= "/$X";
                    }
                }
            }if ($z && ! preg_match('~set|blob|bytea|raw|file|bool~', $o['type'])) {
                $J .= '/SQL';
            }
        }if ($o['auto_increment'] && ! $Ji) {
            $J = lang(50);
        }

return explode('/', $J);
    }

    public function editInput($R, $o, $_a, $Y)
    {
        if ($o['type'] == 'enum') {
            return (isset($_GET['select']) ? "<label><input type='radio'$_a value='-1' checked><i>".lang(8).'</i></label> ' : '').($o['null'] ? "<label><input type='radio'$_a value=''".($Y !== null || isset($_GET['select']) ? '' : ' checked').'><i>NULL</i></label> ' : '').enum_input('radio', $_a, $o, $Y, $Y === 0 ? 0 : null);
        }

return '';
    }

    public function editHint($R, $o, $Y)
    {
        return '';
    }

    public function processInput($o, $Y, $t = '')
    {
        if ($t == 'SQL') {
            return $Y;
        }$C = $o['field'];
        $J = q($Y);
        if (preg_match('~^(now|getdate|uuid)$~', $t)) {
            $J = "$t()";
        } elseif (preg_match('~^current_(date|timestamp)$~', $t)) {
            $J = $t;
        } elseif (preg_match('~^([+-]|\|\|)$~', $t)) {
            $J = idf_escape($C)." $t $J";
        } elseif (preg_match('~^[+-] interval$~', $t)) {
            $J = idf_escape($C)." $t ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i", $Y) ? $Y : $J);
        } elseif (preg_match('~^(addtime|subtime|concat)$~', $t)) {
            $J = "$t(".idf_escape($C).", $J)";
        } elseif (preg_match('~^(md5|sha1|password|encrypt)$~', $t)) {
            $J = "$t($J)";
        }

return unconvert_field($o, $J);
    }

    public function dumpOutput()
    {
        $J = ['text' => lang(64), 'file' => lang(65)];
        if (function_exists('gzencode')) {
            $J['gz'] = 'gzip';
        }

return $J;
    }

    public function dumpFormat()
    {
        return (support('dump') ? ['sql' => 'SQL'] : []) + ['csv' => 'CSV,', 'csv;' => 'CSV;', 'tsv' => 'TSV'];
    }

    public function dumpDatabase($k) {}

    public function dumpTable($R, $Gh, $be = 0)
    {
        if ($_POST['format'] != 'sql') {
            echo "\xef\xbb\xbf";
            if ($Gh) {
                dump_csv(array_keys(fields($R)));
            }
        } else {
            if ($be == 2) {
                $p = [];
                foreach (fields($R) as $C => $o) {
                    $p[] = idf_escape($C)." $o[full_type]";
                }$i = 'CREATE TABLE '.table($R).' ('.implode(', ', $p).')';
            } else {
                $i = create_sql($R, $_POST['auto_increment'], $Gh);
            }set_utf8mb4($i);
            if ($Gh && $i) {
                if ($Gh == 'DROP+CREATE' || $be == 1) {
                    echo 'DROP '.($be == 2 ? 'VIEW' : 'TABLE').' IF EXISTS '.table($R).";\n";
                }if ($be == 1) {
                    $i = remove_definer($i);
                }echo "$i;\n\n";
            }
        }
    }

    public function dumpData($R, $Gh, $H)
    {
        global $g;
        if ($Gh) {
            $Fe = (JUSH == 'sqlite' ? 0 : 1048576);
            $p = [];
            $Gd = false;
            if ($_POST['format'] == 'sql') {
                if ($Gh == 'TRUNCATE+INSERT') {
                    echo truncate_sql($R).";\n";
                }$p = fields($R);
                if (JUSH == 'mssql') {
                    foreach ($p as $o) {
                        if ($o['auto_increment']) {
                            echo 'SET IDENTITY_INSERT '.table($R)." ON;\n";
                            $Gd = true;
                            break;
                        }
                    }
                }
            }$I = $g->query($H, 1);
            if ($I) {
                $Td = '';
                $Oa = '';
                $ge = [];
                $ld = [];
                $Ih = '';
                $Qc = ($R != '' ? 'fetch_assoc' : 'fetch_row');
                while ($K = $I->$Qc()) {
                    if (! $ge) {
                        $Ui = [];
                        foreach ($K as $X) {
                            $o = $I->fetch_field();
                            if ($p[$o->name]['generated']) {
                                $ld[$o->name] = true;

                                continue;
                            }$ge[] = $o->name;
                            $z = idf_escape($o->name);
                            $Ui[] = "$z = VALUES($z)";
                        }$Ih = ($Gh == 'INSERT+UPDATE' ? "\nON DUPLICATE KEY UPDATE ".implode(', ', $Ui) : '').";\n";
                    }if ($_POST['format'] != 'sql') {
                        if ($Gh == 'table') {
                            dump_csv($ge);
                            $Gh = 'INSERT';
                        }dump_csv($K);
                    } else {
                        if (! $Td) {
                            $Td = 'INSERT INTO '.table($R).' ('.implode(', ', array_map('Adminer\idf_escape', $ge)).') VALUES';
                        }foreach ($K as $z => $X) {
                            if ($ld[$z]) {
                                unset($K[$z]);

                                continue;
                            }$o = $p[$z];
                            $K[$z] = ($X !== null ? unconvert_field($o, preg_match(number_type(), $o['type']) && ! preg_match('~\[~', $o['full_type']) && is_numeric($X) ? $X : q(($X === false ? 0 : $X))) : 'NULL');
                        }$Tg = ($Fe ? "\n" : ' ').'('.implode(",\t", $K).')';
                        if (! $Oa) {
                            $Oa = $Td.$Tg;
                        } elseif (strlen($Oa) + 4 + strlen($Tg) + strlen($Ih) < $Fe) {
                            $Oa .= ",$Tg";
                        } else {
                            echo $Oa.$Ih;
                            $Oa = $Td.$Tg;
                        }
                    }
                }if ($Oa) {
                    echo $Oa.$Ih;
                }
            } elseif ($_POST['format'] == 'sql') {
                echo '-- '.str_replace("\n", ' ', $g->error)."\n";
            }if ($Gd) {
                echo 'SET IDENTITY_INSERT '.table($R)." OFF;\n";
            }
        }
    }

    public function dumpFilename($Ed)
    {
        return friendly_url($Ed != '' ? $Ed : (SERVER != '' ? SERVER : 'localhost'));
    }

    public function dumpHeaders($Ed, $Ue = false)
    {
        $Mf = $_POST['output'];
        $Ic = (preg_match('~sql~', $_POST['format']) ? 'sql' : ($Ue ? 'tar' : 'csv'));
        header('Content-Type: '.($Mf == 'gz' ? 'application/x-gzip' : ($Ic == 'tar' ? 'application/x-tar' : ($Ic == 'sql' || $Mf != 'file' ? 'text/plain' : 'text/csv').'; charset=utf-8')));
        if ($Mf == 'gz') {
            ob_start(function ($Q) {
                return gzencode($Q);
            }, 1e6);
        }

return $Ic;
    }

    public function dumpFooter()
    {
        if ($_POST['format'] == 'sql') {
            echo '-- '.gmdate('Y-m-d H:i:s e')."\n";
        }
    }

    public function importServerPath()
    {
        return 'adminer.sql';
    }

    public function homepage()
    {
        echo '<p class="links">'.($_GET['ns'] == '' && support('database') ? '<a href="'.h(ME).'database=">'.lang(66)."</a>\n" : ''),(support('scheme') ? "<a href='".h(ME)."scheme='>".($_GET['ns'] != '' ? lang(67) : lang(68))."</a>\n" : ''),($_GET['ns'] !== '' ? '<a href="'.h(ME).'schema=">'.lang(69)."</a>\n" : ''),(support('privileges') ? "<a href='".h(ME)."privileges='>".lang(70)."</a>\n" : '');

        return true;
    }

    public function navigation($Se)
    {
        global $ia,$bc,$g;
        echo '<h1>
',$this->name(),'<span class="version">
',$ia,' <a href="https://www.adminer.org/#download"',target_blank(),' id="version">',(version_compare($ia, $_COOKIE['adminer_version']) < 0 ? h($_COOKIE['adminer_version']) : ''),'</a>
</span>
</h1>
';
        switch_lang();
        if ($Se == 'auth') {
            $Mf = '';
            foreach ((array) $_SESSION['pwds'] as $Wi => $kh) {
                foreach ($kh as $N => $Ri) {
                    $C = h(get_setting("vendor-$N") ?: $bc[$Wi]);
                    foreach ($Ri as $V => $F) {
                        if ($F !== null) {
                            $Nb = $_SESSION['db'][$Wi][$N][$V];
                            foreach (($Nb ? array_keys($Nb) : ['']) as $k) {
                                $Mf .= "<li><a href='".h(auth_url($Wi, $N, $V, $k))."'>($C) ".h($V.($N != '' ? '@'.$this->serverName($N) : '').($k != '' ? " - $k" : ''))."</a>\n";
                            }
                        }
                    }
                }
            }if ($Mf) {
                echo "<ul id='logins'>\n$Mf</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");
            }
        } else {
            $T = [];
            if ($_GET['ns'] !== '' && ! $Se && DB != '') {
                $g->select_db(DB);
                $T = table_status('', true);
            }$this->syntaxHighlighting($T);
            $this->databasesPrint($Se);
            $ma = [];
            if (DB == '' || ! $Se) {
                if (support('sql')) {
                    $ma[] = "<a href='".h(ME)."sql='".bold(isset($_GET['sql']) && ! isset($_GET['import'])).'>'.lang(63).'</a>';
                    $ma[] = "<a href='".h(ME)."import='".bold(isset($_GET['import'])).'>'.lang(71).'</a>';
                }$ma[] = "<a href='".h(ME).'dump='.urlencode(isset($_GET['table']) ? $_GET['table'] : $_GET['select'])."' id='dump'".bold(isset($_GET['dump'])).'>'.lang(72).'</a>';
            }$Kd = $_GET['ns'] !== '' && ! $Se && DB != '';
            if ($Kd) {
                $ma[] = '<a href="'.h(ME).'create="'.bold($_GET['create'] === '').'>'.lang(73).'</a>';
            }echo $ma ? "<p class='links'>\n".implode("\n", $ma)."\n" : '';
            if ($Kd) {
                if ($T) {
                    $this->tablesPrint($T);
                } else {
                    echo "<p class='message'>".lang(9)."</p>\n";
                }
            }
        }
    }

    public function syntaxHighlighting($T)
    {
        global $g;
        echo script_src(preg_replace('~\\?.*~', '', ME).'?file=jush.js&version=5.0.6');
        if (support('sql')) {
            echo '<script'.nonce().">\n";
            if ($T) {
                $we = [];
                foreach ($T as $R => $U) {
                    $we[] = preg_quote($R, '/');
                }echo 'var jushLinks = { '.JUSH.": [ '".js_escape(ME).(support('table') ? 'table=' : 'select=')."\$&', /\\b(".implode('|', $we).")\\b/g ] };\n";
                foreach (['bac', 'bra', 'sqlite_quo', 'mssql_bra'] as $X) {
                    echo "jushLinks.$X = jushLinks.".JUSH.";\n";
                }
            }echo "</script>\n";
        }echo script("bodyLoad('".(is_object($g) ? preg_replace('~^(\d\.?\d).*~s', '\1', $g->server_info) : '')."'".($g->maria ? ', true' : '').');');
    }

    public function databasesPrint($Se)
    {
        global $b,$g;
        $j = $this->databases();
        if (DB && $j && ! in_array(DB, $j)) {
            array_unshift($j, DB);
        }echo '<form action="">
<p id="dbs">
';
        hidden_fields_get();
        $Lb = script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");
        echo "<span title='".lang(36)."'>".lang(74).':</span> '.($j ? html_select('db', ['' => ''] + $j, DB).$Lb : "<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n"),"<input type='submit' value='".lang(20)."'".($j ? " class='hidden'" : '').">\n";
        if (support('scheme')) {
            if ($Se != 'db' && DB != '' && $g->select_db(DB)) {
                echo '<br><span>'.lang(75).':</span> '.html_select('ns', ['' => ''] + $b->schemas(), $_GET['ns']).$Lb;
                if ($_GET['ns'] != '') {
                    set_schema($_GET['ns']);
                }
            }
        }foreach (['import', 'sql', 'schema', 'dump', 'privileges'] as $X) {
            if (isset($_GET[$X])) {
                echo "<input type='hidden' name='$X' value=''>";
                break;
            }
        }echo "</p></form>\n";
    }

    public function tablesPrint($T)
    {
        echo "<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");
        foreach ($T as $R => $P) {
            $C = $this->tableName($P);
            if ($C != '') {
                echo '<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET['select'] == $R || $_GET['edit'] == $R, 'select')." title='".lang(40)."'>".lang(76).'</a> ',(support('table') || support('indexes') ? '<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R, [$_GET['table'], $_GET['create'], $_GET['indexes'], $_GET['foreign'], $_GET['trigger']]), (is_view($P) ? 'view' : 'structure'))." title='".lang(41)."'>$C</a>" : "<span>$C</span>")."\n";
            }
        }echo "</ul>\n";
    }
}$b = (function_exists('adminer_object') ? adminer_object() : new Adminer);
$bc = ['server' => 'MySQL / MariaDB'] + $bc;
if (! defined('Adminer\DRIVER')) {
    define('Adminer\DRIVER', 'server');
    if (extension_loaded('mysqli')) {
        class Db extends \MySQLi
        {
            public $extension = 'MySQLi';

            public function __construct()
            {
                parent::init();
            }

            public function connect($N = '', $V = '', $F = '', $Kb = null, $eg = null, $sh = null)
            {
                global $b;
                mysqli_report(MYSQLI_REPORT_OFF);
                [$Cd, $eg] = explode(':', $N, 2);
                $Bh = $b->connectSsl();
                if ($Bh) {
                    $this->ssl_set($Bh['key'], $Bh['cert'], $Bh['ca'], '', '');
                }$J = @$this->real_connect(($N != '' ? $Cd : ini_get('mysqli.default_host')), ($N.$V != '' ? $V : ini_get('mysqli.default_user')), ($N.$V.$F != '' ? $F : ini_get('mysqli.default_pw')), $Kb, (is_numeric($eg) ? $eg : ini_get('mysqli.default_port')), (! is_numeric($eg) ? $eg : $sh), ($Bh ? ($Bh['verify'] !== false ? 2048 : 64) : 0));
                $this->options(MYSQLI_OPT_LOCAL_INFILE, false);

                return $J;
            }

            public function set_charset($Ua)
            {
                if (parent::set_charset($Ua)) {
                    return true;
                }parent::set_charset('utf8');

                return $this->query("SET NAMES $Ua");
            }

            public function result($H, $o = 0)
            {
                $I = $this->query($H);
                if (! $I) {
                    return false;
                }$K = $I->fetch_array();

                return $K[$o];
            }

            public function quote($Q)
            {
                return "'".$this->escape_string($Q)."'";
            }
        }
    } elseif (extension_loaded('mysql') && ! ((ini_bool('sql.safe_mode') || ini_bool('mysql.allow_local_infile')) && extension_loaded('pdo_mysql'))) {
        class Db
        {
            public $extension = 'MySQL';

            public $server_infovar;

            public $affected_rowsvar;

            public $errnovar;

            public $errorvar;

            private $link;

            public $resultprivate;

            public function connect($N, $V, $F)
            {
                if (ini_bool('mysql.allow_local_infile')) {
                    $this->error = lang(77, "'mysql.allow_local_infile'", 'MySQLi', 'PDO_MySQL');

                    return false;
                }$this->link = @mysql_connect(($N != '' ? $N : ini_get('mysql.default_host')), ("$N$V" != '' ? $V : ini_get('mysql.default_user')), ("$N$V$F" != '' ? $F : ini_get('mysql.default_password')), true, 131072);
                if ($this->link) {
                    $this->server_info = mysql_get_server_info($this->link);
                } else {
                    $this->error = mysql_error();
                }

return (bool) $this->link;
            }

            public function set_charset($Ua)
            {
                if (function_exists('mysql_set_charset')) {
                    if (mysql_set_charset($Ua, $this->link)) {
                        return true;
                    }mysql_set_charset('utf8', $this->link);
                }

return $this->query("SET NAMES $Ua");
            }

            public function quote($Q)
            {
                return "'".mysql_real_escape_string($Q, $this->link)."'";
            }

            public function select_db($Kb)
            {
                return mysql_select_db($Kb, $this->link);
            }

            public function query($H, $Bi = false)
            {
                $I = @($Bi ? mysql_unbuffered_query($H, $this->link) : mysql_query($H, $this->link));
                $this->error = '';
                if (! $I) {
                    $this->errno = mysql_errno($this->link);
                    $this->error = mysql_error($this->link);

                    return false;
                }if ($I === true) {
                    $this->affected_rows = mysql_affected_rows($this->link);
                    $this->info = mysql_info($this->link);

                    return true;
                }

return new Result($I);
            }

            public function multi_query($H)
            {
                return $this->result = $this->query($H);
            }

            public function store_result()
            {
                return $this->result;
            }

            public function next_result()
            {
                return false;
            }

            public function result($H, $o = 0)
            {
                $I = $this->query($H);

                return $I ? $I->fetch_column($o) : false;
            }
        }class Result
        {
            public $num_rows;

            private $result;

            public $offsetprivate = 0;

            public function __construct($I)
            {
                $this->result = $I;
                $this->num_rows = mysql_num_rows($I);
            }

            public function fetch_assoc()
            {
                return mysql_fetch_assoc($this->result);
            }

            public function fetch_row()
            {
                return mysql_fetch_row($this->result);
            }

            public function fetch_column($o)
            {
                return $this->num_rows ? mysql_result($this->result, 0, $o) : false;
            }

            public function fetch_field()
            {
                $J = mysql_fetch_field($this->result, $this->offset++);
                $J->orgtable = $J->table;
                $J->orgname = $J->name;
                $J->charsetnr = ($J->blob ? 63 : 0);

                return $J;
            }

            public function __destruct()
            {
                mysql_free_result($this->result);
            }
        }
    } elseif (extension_loaded('pdo_mysql')) {
        class Db extends PdoDb
        {
            public $extension = 'PDO_MySQL';

            public function connect($N, $V, $F)
            {
                global $b;
                $yf = [\PDO::MYSQL_ATTR_LOCAL_INFILE => false];
                $Bh = $b->connectSsl();
                if ($Bh) {
                    if ($Bh['key']) {
                        $yf[\PDO::MYSQL_ATTR_SSL_KEY] = $Bh['key'];
                    }if ($Bh['cert']) {
                        $yf[\PDO::MYSQL_ATTR_SSL_CERT] = $Bh['cert'];
                    }if ($Bh['ca']) {
                        $yf[\PDO::MYSQL_ATTR_SSL_CA] = $Bh['ca'];
                    }if (isset($Bh['verify'])) {
                        $yf[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $Bh['verify'];
                    }
                }$this->dsn('mysql:charset=utf8;host='.str_replace(':', ';unix_socket=', preg_replace('~:(\d)~', ';port=\1', $N)), $V, $F, $yf);

                return true;
            }

            public function set_charset($Ua)
            {
                $this->query("SET NAMES $Ua");
            }

            public function select_db($Kb)
            {
                return $this->query('USE '.idf_escape($Kb));
            }

            public function query($H, $Bi = false)
            {
                $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, ! $Bi);

                return parent::query($H, $Bi);
            }
        }
    }class Driver extends SqlDriver
    {
        public static $ig = ['MySQLi', 'MySQL', 'PDO_MySQL'];

        public static $de = 'sql';

        public $unsigned = ['unsigned', 'zerofill', 'unsigned zerofill'];

        public $operators = ['=', '<', '>', '<=', '>=', '!=', 'LIKE', 'LIKE %%', 'REGEXP', 'IN', 'FIND_IN_SET', 'IS NULL', 'NOT LIKE', 'NOT REGEXP', 'NOT IN', 'IS NOT NULL', 'SQL'];

        public $functions = ['char_length', 'date', 'from_unixtime', 'lower', 'round', 'floor', 'ceil', 'sec_to_time', 'time_to_sec', 'upper'];

        public $grouping = ['avg', 'count', 'count distinct', 'group_concat', 'max', 'min', 'sum'];

        public function __construct($g)
        {
            parent::__construct($g);
            $this->types = [lang(25) => ['tinyint' => 3, 'smallint' => 5, 'mediumint' => 8, 'int' => 10, 'bigint' => 20, 'decimal' => 66, 'float' => 12, 'double' => 21], lang(26) => ['date' => 10, 'datetime' => 19, 'timestamp' => 19, 'time' => 10, 'year' => 4], lang(27) => ['char' => 255, 'varchar' => 65535, 'tinytext' => 255, 'text' => 65535, 'mediumtext' => 16777215, 'longtext' => 4294967295], lang(78) => ['enum' => 65535, 'set' => 64], lang(28) => ['bit' => 20, 'binary' => 255, 'varbinary' => 65535, 'tinyblob' => 255, 'blob' => 65535, 'mediumblob' => 16777215, 'longblob' => 4294967295], lang(30) => ['geometry' => 0, 'point' => 0, 'linestring' => 0, 'polygon' => 0, 'multipoint' => 0, 'multilinestring' => 0, 'multipolygon' => 0, 'geometrycollection' => 0]];
            $this->editFunctions = [['char' => 'md5/sha1/password/encrypt/uuid', 'binary' => 'md5/sha1', 'date|time' => 'now'], [number_type() => '+/-', 'date' => '+ interval/- interval', 'time' => 'addtime/subtime', 'char|text' => 'concat']];
            if (min_version('5.7.8', 10.2, $g)) {
                $this->types[lang(27)]['json'] = 4294967295;
            }if (min_version('', 10.7, $g)) {
                $this->types[lang(27)]['uuid'] = 128;
                $this->editFunctions[0]['uuid'] = 'uuid';
            }if (min_version(9, '', $g)) {
                $this->types[lang(25)]['vector'] = 16383;
                $this->editFunctions[0]['vector'] = 'string_to_vector';
            }if (min_version(5.7, 10.2, $g)) {
                $this->generated = ['STORED', 'VIRTUAL'];
            }
        }

        public function unconvertFunction($o)
        {
            return preg_match('~binary~', $o['type']) ? "<code class='jush-sql'>UNHEX</code>" : ($o['type'] == 'bit' ? doc_link(['sql' => 'bit-value-literals.html'], "<code>b''</code>") : (preg_match('~geometry|point|linestring|polygon~', $o['type']) ? "<code class='jush-sql'>GeomFromText</code>" : ''));
        }

        public function insert($R, $O)
        {
            return $O ? parent::insert($R, $O) : queries('INSERT INTO '.table($R)." ()\nVALUES ()");
        }

        public function insertUpdate($R, $L, $G)
        {
            $e = array_keys(reset($L));
            $kg = 'INSERT INTO '.table($R).' ('.implode(', ', $e).") VALUES\n";
            $Ui = [];
            foreach ($e as $z) {
                $Ui[$z] = "$z = VALUES($z)";
            }$Ih = "\nON DUPLICATE KEY UPDATE ".implode(', ', $Ui);
            $Ui = [];
            $te = 0;
            foreach ($L as $O) {
                $Y = '('.implode(', ', $O).')';
                if ($Ui && (strlen($kg) + $te + strlen($Y) + strlen($Ih) > 1e6)) {
                    if (! queries($kg.implode(",\n", $Ui).$Ih)) {
                        return false;
                    }$Ui = [];
                    $te = 0;
                }$Ui[] = $Y;
                $te += strlen($Y) + 2;
            }

return queries($kg.implode(",\n", $Ui).$Ih);
        }

        public function slowQuery($H, $ei)
        {
            if (min_version('5.7.8', '10.1.2')) {
                if ($this->conn->maria) {
                    return "SET STATEMENT max_statement_time=$ei FOR $H";
                } elseif (preg_match('~^(SELECT\b)(.+)~is', $H, $B)) {
                    return "$B[1] /*+ MAX_EXECUTION_TIME(".($ei * 1000).") */ $B[2]";
                }
            }
        }

        public function convertSearch($w, $X, $o)
        {
            return preg_match('~char|text|enum|set~', $o['type']) && ! preg_match('~^utf8~', $o['collation']) && preg_match('~[\x80-\xFF]~', $X['val']) ? "CONVERT($w USING ".charset($this->conn).')' : $w;
        }

        public function warnings()
        {
            $I = $this->conn->query('SHOW WARNINGS');
            if ($I && $I->num_rows) {
                ob_start();
                select($I);

                return ob_get_clean();
            }
        }

        public function tableHelp($C, $be = false)
        {
            $_e = $this->conn->maria;
            if (information_schema(DB)) {
                return strtolower('information-schema-'.($_e ? "$C-table/" : str_replace('_', '-', $C).'-table.html'));
            }if (DB == 'mysql') {
                return $_e ? "mysql$C-table/" : 'system-schema.html';
            }
        }

        public function hasCStyleEscapes()
        {
            static $Qa;
            if ($Qa === null) {
                $_h = $this->conn->result("SHOW VARIABLES LIKE 'sql_mode'", 1);
                $Qa = (strpos($_h, 'NO_BACKSLASH_ESCAPES') === false);
            }

return $Qa;
        }
    }function idf_escape($w)
    {
        return '`'.str_replace('`', '``', $w).'`';
    }function table($w)
    {
        return idf_escape($w);
    }function connect($Cb)
    {
        global $bc;
        $g = new Db;
        if ($g->connect($Cb[0], $Cb[1], $Cb[2])) {
            $g->set_charset(charset($g));
            $g->query('SET sql_quote_show_create = 1, autocommit = 1');
            $g->maria = preg_match('~MariaDB~', $g->server_info);
            $bc[DRIVER] = ($g->maria ? 'MariaDB' : 'MySQL');

            return $g;
        }$J = $g->error;
        if (function_exists('iconv') && ! is_utf8($J) && strlen($Tg = iconv('windows-1250', 'utf-8', $J)) > strlen($J)) {
            $J = $Tg;
        }

return $J;
    }function get_databases($ad)
    {
        $J = get_session('dbs');
        if ($J === null) {
            $H = 'SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME';
            $J = ($ad ? slow_query($H) : get_vals($H));
            restart_session();
            set_session('dbs', $J);
            stop_session();
        }

return $J;
    }function limit($H, $Z, $_, $D = 0, $fh = ' ')
    {
        return " $H$Z".($_ !== null ? $fh."LIMIT $_".($D ? " OFFSET $D" : '') : '');
    }function limit1($R, $H, $Z, $fh = "\n")
    {
        return limit($H, $Z, 1, 0, $fh);
    }function db_collation($k, $ib)
    {
        $J = null;
        $i = get_val('SHOW CREATE DATABASE '.idf_escape($k), 1);
        if (preg_match('~ COLLATE ([^ ]+)~', $i, $B)) {
            $J = $B[1];
        } elseif (preg_match('~ CHARACTER SET ([^ ]+)~', $i, $B)) {
            $J = $ib[$B[1]][-1];
        }

return $J;
    }function engines()
    {
        $J = [];
        foreach (get_rows('SHOW ENGINES') as $K) {
            if (preg_match('~YES|DEFAULT~', $K['Support'])) {
                $J[] = $K['Engine'];
            }
        }

return $J;
    }function logged_user()
    {
        return get_val('SELECT USER()');
    }function tables_list()
    {
        return get_key_vals('SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME');
    }function count_tables($j)
    {
        $J = [];
        foreach ($j as $k) {
            $J[$k] = count(get_vals('SHOW TABLES IN '.idf_escape($k)));
        }

return $J;
    }function table_status($C = '', $Oc = false)
    {
        $J = [];
        foreach (get_rows($Oc ? 'SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() '.($C != '' ? 'AND TABLE_NAME = '.q($C) : 'ORDER BY Name') : 'SHOW TABLE STATUS'.($C != '' ? ' LIKE '.q(addcslashes($C, '%_\\')) : '')) as $K) {
            if ($K['Engine'] == 'InnoDB') {
                $K['Comment'] = preg_replace('~(?:(.+); )?InnoDB free: .*~', '\1', $K['Comment']);
            }if (! isset($K['Engine'])) {
                $K['Comment'] = '';
            }if ($C != '') {
                $K['Name'] = $C;

                return $K;
            }$J[$K['Name']] = $K;
        }

return $J;
    }function is_view($S)
    {
        return $S['Engine'] === null;
    }function fk_support($S)
    {
        return preg_match('~InnoDB|IBMDB2I~i', $S['Engine']) || (preg_match('~NDB~i', $S['Engine']) && min_version(5.6));
    }function fields($R)
    {
        global $g;
        $_e = $g->maria;
        $J = [];
        foreach (get_rows('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '.q($R).' ORDER BY ORDINAL_POSITION') as $K) {
            $o = $K['COLUMN_NAME'];
            $U = $K['COLUMN_TYPE'];
            $md = $K['GENERATION_EXPRESSION'];
            $Lc = $K['EXTRA'];
            preg_match('~^(VIRTUAL|PERSISTENT|STORED)~', $Lc, $ld);
            preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~', $U, $Ce);
            $l = $K['COLUMN_DEFAULT'];
            if ($l != '') {
                $ae = preg_match('~text|json~', $Ce[1]);
                if (! $_e && $ae) {
                    $l = preg_replace("~^(_\w+)?('.*')$~", '\2', stripslashes($l));
                }if ($_e || $ae) {
                    $l = ($l == 'NULL' ? null : preg_replace_callback("~^'(.*)'$~", function ($B) {
                        return stripslashes(str_replace("''", "'", $B[1]));
                    }, $l));
                }if (! $_e && preg_match('~binary~', $Ce[1]) && preg_match('~^0x(\w*)$~', $l, $B)) {
                    $l = pack('H*', $B[1]);
                }
            }$J[$o] = ['field' => $o, 'full_type' => $U, 'type' => $Ce[1], 'length' => $Ce[2], 'unsigned' => ltrim($Ce[3].$Ce[4]), 'default' => ($ld ? ($_e ? $md : stripslashes($md)) : $l), 'null' => ($K['IS_NULLABLE'] == 'YES'), 'auto_increment' => ($Lc == 'auto_increment'), 'on_update' => (preg_match('~\bon update (\w+)~i', $Lc, $B) ? $B[1] : ''), 'collation' => $K['COLLATION_NAME'], 'privileges' => array_flip(explode(',', "$K[PRIVILEGES],where,order")), 'comment' => $K['COLUMN_COMMENT'], 'primary' => ($K['COLUMN_KEY'] == 'PRI'), 'generated' => ($ld[1] == 'PERSISTENT' ? 'STORED' : $ld[1])];
        }

return $J;
    }function indexes($R, $h = null)
    {
        $J = [];
        foreach (get_rows('SHOW INDEX FROM '.table($R), $h) as $K) {
            $C = $K['Key_name'];
            $J[$C]['type'] = ($C == 'PRIMARY' ? 'PRIMARY' : ($K['Index_type'] == 'FULLTEXT' ? 'FULLTEXT' : ($K['Non_unique'] ? ($K['Index_type'] == 'SPATIAL' ? 'SPATIAL' : 'INDEX') : 'UNIQUE')));
            $J[$C]['columns'][] = $K['Column_name'];
            $J[$C]['lengths'][] = ($K['Index_type'] == 'SPATIAL' ? null : $K['Sub_part']);
            $J[$C]['descs'][] = null;
        }

return $J;
    }function foreign_keys($R)
    {
        global $m;
        static $ag = '(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';
        $J = [];
        $Ab = get_val('SHOW CREATE TABLE '.table($R), 1);
        if ($Ab) {
            preg_match_all("~CONSTRAINT ($ag) FOREIGN KEY ?\\(((?:$ag,? ?)+)\\) REFERENCES ($ag)(?:\\.($ag))? \\(((?:$ag,? ?)+)\\)(?: ON DELETE ($m->onActions))?(?: ON UPDATE ($m->onActions))?~", $Ab, $De, PREG_SET_ORDER);
            foreach ($De as $B) {
                preg_match_all("~$ag~", $B[2], $uh);
                preg_match_all("~$ag~", $B[5], $Wh);
                $J[idf_unescape($B[1])] = ['db' => idf_unescape($B[4] != '' ? $B[3] : $B[4]), 'table' => idf_unescape($B[4] != '' ? $B[4] : $B[3]), 'source' => array_map('Adminer\idf_unescape', $uh[0]), 'target' => array_map('Adminer\idf_unescape', $Wh[0]), 'on_delete' => ($B[6] ?: 'RESTRICT'), 'on_update' => ($B[7] ?: 'RESTRICT')];
            }
        }

return $J;
    }function view($C)
    {
        return ['select' => preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU', '', get_val('SHOW CREATE VIEW '.table($C), 1))];
    }function collations()
    {
        $J = [];
        foreach (get_rows('SHOW COLLATION') as $K) {
            if ($K['Default']) {
                $J[$K['Charset']][-1] = $K['Collation'];
            } else {
                $J[$K['Charset']][] = $K['Collation'];
            }
        }ksort($J);
        foreach ($J as $z => $X) {
            asort($J[$z]);
        }

return $J;
    }function information_schema($k)
    {
        return ($k == 'information_schema') || (min_version(5.5) && $k == 'performance_schema');
    }function error()
    {
        global $g;

        return h(preg_replace('~^You have an error.*syntax to use~U', 'Syntax error', $g->error));
    }function create_database($k, $hb)
    {
        return queries('CREATE DATABASE '.idf_escape($k).($hb ? ' COLLATE '.q($hb) : ''));
    }function drop_databases($j)
    {
        $J = apply_queries('DROP DATABASE', $j, 'Adminer\idf_escape');
        restart_session();
        set_session('dbs', null);

        return $J;
    }function rename_database($C, $hb)
    {
        $J = false;
        if (create_database($C, $hb)) {
            $T = [];
            $Zi = [];
            foreach (tables_list() as $R => $U) {
                if ($U == 'VIEW') {
                    $Zi[] = $R;
                } else {
                    $T[] = $R;
                }
            }$J = (! $T && ! $Zi) || move_tables($T, $Zi, $C);
            drop_databases($J ? [DB] : []);
        }

return $J;
    }function auto_increment()
    {
        $Ca = ' PRIMARY KEY';
        if ($_GET['create'] != '' && $_POST['auto_increment_col']) {
            foreach (indexes($_GET['create']) as $x) {
                if (in_array($_POST['fields'][$_POST['auto_increment_col']]['orig'], $x['columns'], true)) {
                    $Ca = '';
                    break;
                }if ($x['type'] == 'PRIMARY') {
                    $Ca = ' UNIQUE';
                }
            }
        }

return " AUTO_INCREMENT$Ca";
    }function alter_table($R, $C, $p, $cd, $nb, $sc, $hb, $Ba, $Wf)
    {
        global $g;
        $c = [];
        foreach ($p as $o) {
            if ($o[1]) {
                $l = $o[1][3];
                if (preg_match('~ GENERATED~', $l)) {
                    $o[1][3] = ($g->maria ? '' : $o[1][2]);
                    $o[1][2] = $l;
                }$c[] = ($R != '' ? ($o[0] != '' ? 'CHANGE '.idf_escape($o[0]) : 'ADD') : ' ').' '.implode($o[1]).($R != '' ? $o[2] : '');
            } else {
                $c[] = 'DROP '.idf_escape($o[0]);
            }
        }$c = array_merge($c, $cd);
        $P = ($nb !== null ? ' COMMENT='.q($nb) : '').($sc ? ' ENGINE='.q($sc) : '').($hb ? ' COLLATE '.q($hb) : '').($Ba != '' ? " AUTO_INCREMENT=$Ba" : '');
        if ($R == '') {
            return queries('CREATE TABLE '.table($C)." (\n".implode(",\n", $c)."\n)$P$Wf");
        }if ($R != $C) {
            $c[] = 'RENAME TO '.table($C);
        }if ($P) {
            $c[] = ltrim($P);
        }

return $c || $Wf ? queries('ALTER TABLE '.table($R)."\n".implode(",\n", $c).$Wf) : true;
    }function alter_indexes($R, $c)
    {
        foreach ($c as $z => $X) {
            $c[$z] = ($X[2] == 'DROP' ? "\nDROP INDEX ".idf_escape($X[1]) : "\nADD $X[0] ".($X[0] == 'PRIMARY' ? 'KEY ' : '').($X[1] != '' ? idf_escape($X[1]).' ' : '').'('.implode(', ', $X[2]).')');
        }

return queries('ALTER TABLE '.table($R).implode(',', $c));
    }function truncate_tables($T)
    {
        return apply_queries('TRUNCATE TABLE', $T);
    }function drop_views($Zi)
    {
        return queries('DROP VIEW '.implode(', ', array_map('Adminer\table', $Zi)));
    }function drop_tables($T)
    {
        return queries('DROP TABLE '.implode(', ', array_map('Adminer\table', $T)));
    }function move_tables($T, $Zi, $Wh)
    {
        global $g;
        $Hg = [];
        foreach ($T as $R) {
            $Hg[] = table($R).' TO '.idf_escape($Wh).'.'.table($R);
        }if (! $Hg || queries('RENAME TABLE '.implode(', ', $Hg))) {
            $Rb = [];
            foreach ($Zi as $R) {
                $Rb[table($R)] = view($R);
            }$g->select_db($Wh);
            $k = idf_escape(DB);
            foreach ($Rb as $C => $Yi) {
                if (! queries("CREATE VIEW $C AS ".str_replace(" $k.", ' ', $Yi['select'])) || ! queries("DROP VIEW $k.$C")) {
                    return false;
                }
            }

return true;
        }

return false;
    }function copy_tables($T, $Zi, $Wh)
    {
        queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
        foreach ($T as $R) {
            $C = ($Wh == DB ? table("copy_$R") : idf_escape($Wh).'.'.table($R));
            if (($_POST['overwrite'] && ! queries("\nDROP TABLE IF EXISTS $C")) || ! queries("CREATE TABLE $C LIKE ".table($R)) || ! queries("INSERT INTO $C SELECT * FROM ".table($R))) {
                return false;
            }foreach (get_rows('SHOW TRIGGERS LIKE '.q(addcslashes($R, '%_\\'))) as $K) {
                $vi = $K['Trigger'];
                if (! queries('CREATE TRIGGER '.($Wh == DB ? idf_escape("copy_$vi") : idf_escape($Wh).'.'.idf_escape($vi))." $K[Timing] $K[Event] ON $C FOR EACH ROW\n$K[Statement];")) {
                    return false;
                }
            }
        }foreach ($Zi as $R) {
            $C = ($Wh == DB ? table("copy_$R") : idf_escape($Wh).'.'.table($R));
            $Yi = view($R);
            if (($_POST['overwrite'] && ! queries("DROP VIEW IF EXISTS $C")) || ! queries("CREATE VIEW $C AS $Yi[select]")) {
                return false;
            }
        }

return true;
    }function trigger($C)
    {
        if ($C == '') {
            return [];
        }$L = get_rows('SHOW TRIGGERS WHERE `Trigger` = '.q($C));

        return reset($L);
    }function triggers($R)
    {
        $J = [];
        foreach (get_rows('SHOW TRIGGERS LIKE '.q(addcslashes($R, '%_\\'))) as $K) {
            $J[$K['Trigger']] = [$K['Timing'], $K['Event']];
        }

return $J;
    }function trigger_options()
    {
        return ['Timing' => ['BEFORE', 'AFTER'], 'Event' => ['INSERT', 'UPDATE', 'DELETE'], 'Type' => ['FOR EACH ROW']];
    }function routine($C, $U)
    {
        global $m;
        $ua = ['bool', 'boolean', 'integer', 'double precision', 'real', 'dec', 'numeric', 'fixed', 'national char', 'national varchar'];
        $vh = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
        $uc = $m->enumLength;
        $_i = '(('.implode('|', array_merge(array_keys($m->types()), $ua)).")\\b(?:\\s*\\(((?:[^'\")]|$uc)++)\\))?"."\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";
        $ag = "$vh*(".($U == 'FUNCTION' ? '' : $m->inout).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$_i";
        $i = get_val("SHOW CREATE $U ".idf_escape($C), 2);
        preg_match("~\\(((?:$ag\\s*,?)*)\\)\\s*".($U == 'FUNCTION' ? "RETURNS\\s+$_i\\s+" : '').'(.*)~is', $i, $B);
        $p = [];
        preg_match_all("~$ag\\s*,?~is", $B[1], $De, PREG_SET_ORDER);
        foreach ($De as $Qf) {
            $p[] = ['field' => str_replace('``', '`', $Qf[2]).$Qf[3], 'type' => strtolower($Qf[5]), 'length' => preg_replace_callback("~$uc~s", 'Adminer\normalize_enum', $Qf[6]), 'unsigned' => strtolower(preg_replace('~\s+~', ' ', trim("$Qf[8] $Qf[7]"))), 'null' => 1, 'full_type' => $Qf[4], 'inout' => strtoupper($Qf[1]), 'collation' => strtolower($Qf[9])];
        }

return ['fields' => $p, 'comment' => get_val('SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = '.q($C))] + ($U != 'FUNCTION' ? ['definition' => $B[11]] : ['returns' => ['type' => $B[12], 'length' => $B[13], 'unsigned' => $B[15], 'collation' => $B[16]], 'definition' => $B[17], 'language' => 'SQL']);
    }function routines()
    {
        return get_rows('SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()');
    }function routine_languages()
    {
        return [];
    }function routine_id($C, $K)
    {
        return idf_escape($C);
    }function last_id()
    {
        return get_val('SELECT LAST_INSERT_ID()');
    }function explain($g, $H)
    {
        return $g->query('EXPLAIN '.(min_version(5.1) && ! min_version(5.7) ? 'PARTITIONS ' : '').$H);
    }function found_rows($S, $Z)
    {
        return $Z || $S['Engine'] != 'InnoDB' ? null : $S['Rows'];
    }function create_sql($R, $Ba, $Gh)
    {
        $J = get_val('SHOW CREATE TABLE '.table($R), 1);
        if (! $Ba) {
            $J = preg_replace('~ AUTO_INCREMENT=\d+~', '', $J);
        }

return $J;
    }function truncate_sql($R)
    {
        return 'TRUNCATE '.table($R);
    }function use_sql($Kb)
    {
        return 'USE '.idf_escape($Kb);
    }function trigger_sql($R)
    {
        $J = '';
        foreach (get_rows('SHOW TRIGGERS LIKE '.q(addcslashes($R, '%_\\')), null, '-- ') as $K) {
            $J .= "\nCREATE TRIGGER ".idf_escape($K['Trigger'])." $K[Timing] $K[Event] ON ".table($K['Table'])." FOR EACH ROW\n$K[Statement];;\n";
        }

return $J;
    }function show_variables()
    {
        return get_key_vals('SHOW VARIABLES');
    }function process_list()
    {
        return get_rows('SHOW FULL PROCESSLIST');
    }function show_status()
    {
        return get_key_vals('SHOW STATUS');
    }function convert_field($o)
    {
        if (preg_match('~binary~', $o['type'])) {
            return 'HEX('.idf_escape($o['field']).')';
        }if ($o['type'] == 'bit') {
            return 'BIN('.idf_escape($o['field']).' + 0)';
        }if (preg_match('~geometry|point|linestring|polygon~', $o['type'])) {
            return (min_version(8) ? 'ST_' : '').'AsWKT('.idf_escape($o['field']).')';
        }
    }function unconvert_field($o, $J)
    {
        if (preg_match('~binary~', $o['type'])) {
            $J = "UNHEX($J)";
        }if ($o['type'] == 'bit') {
            $J = "CONVERT(b$J, UNSIGNED)";
        }if (preg_match('~geometry|point|linestring|polygon~', $o['type'])) {
            $kg = (min_version(8) ? 'ST_' : '');
            $J = $kg."GeomFromText($J, $kg"."SRID($o[field]))";
        }

return $J;
    }function support($Pc)
    {
        return ! preg_match('~scheme|sequence|type|view_trigger|materializedview'.(min_version(8) ? '' : '|descidx'.(min_version(5.1) ? '' : '|event|partitioning')).(min_version('8.0.16', '10.2.1') ? '' : '|check').'~', $Pc);
    }function kill_process($X)
    {
        return queries('KILL '.number($X));
    }function connection_id()
    {
        return 'SELECT CONNECTION_ID()';
    }function max_connections()
    {
        return get_val('SELECT @@max_connections');
    }
}define('Adminer\JUSH', Driver::$de);
define('Adminer\SERVER', $_GET[DRIVER]);
define('Adminer\DB', $_GET['db']);
define('Adminer\ME', preg_replace('~\?.*~', '', relative_uri()).'?'.(sid() ? SID.'&' : '').(SERVER !== null ? DRIVER.'='.urlencode(SERVER).'&' : '').(isset($_GET['username']) ? 'username='.urlencode($_GET['username']).'&' : '').(DB != '' ? 'db='.urlencode(DB).'&'.(isset($_GET['ns']) ? 'ns='.urlencode($_GET['ns']).'&' : '') : ''));
if (! ob_get_level()) {
    ob_start(null, 4096);
}function page_header($gi, $n = '', $Na = [], $hi = '')
{
    global $ca,$ia,$b,$bc;
    page_headers();
    if (is_ajax() && $n) {
        page_messages($n);
        exit;
    }$ii = $gi.($hi != '' ? ": $hi" : '');
    $ji = strip_tags($ii.(SERVER != '' && SERVER != 'localhost' ? h(' - '.SERVER) : '').' - '.$b->name());
    echo '<!DOCTYPE html>
<html lang="',$ca,'" dir="',lang(79),'">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width">
<title>',$ji,'</title>
<link rel="stylesheet" href="',h(preg_replace('~\\?.*~', '', ME).'?file=default.css&version=5.0.6'),'">
';
    $Eb = $b->css();
    $Hb = (count($Eb) == 1 ? (bool) preg_match('~-dark~', $Eb[0]) : null);
    if ($Hb !== false) {
        echo "<link rel='stylesheet'".($Hb ? '' : " media='(prefers-color-scheme: dark)'")." href='".h(preg_replace('~\\?.*~', '', ME).'?file=dark.css&version=5.0.6')."'>\n";
    }echo "<meta name='color-scheme' content='".($Hb === null ? 'light dark' : ($Hb ? 'dark' : 'light'))."'>\n",script_src(preg_replace('~\\?.*~', '', ME).'?file=functions.js&version=5.0.6');
    if ($b->head($Hb)) {
        echo "<link rel='shortcut icon' type='image/x-icon' href='".h(preg_replace('~\\?.*~', '', ME).'?file=favicon.ico&version=5.0.6')."'>\n","<link rel='apple-touch-icon' href='".h(preg_replace('~\\?.*~', '', ME).'?file=favicon.ico&version=5.0.6')."'>\n";
    }foreach ($Eb as $X) {
        echo "<link rel='stylesheet'".(preg_match('~-dark~', $X) && ! $Hb ? " media='(prefers-color-scheme: dark)'" : '')." href='".h($X)."'>\n";
    }echo "\n<body class='".lang(79)." nojs'>\n";
    $q = get_temp_dir().'/adminer.version';
    if (! $_COOKIE['adminer_version'] && function_exists('openssl_verify') && file_exists($q) && filemtime($q) + 86400 > time()) {
        $Xi = unserialize(file_get_contents($q));
        $tg = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
';
        if (openssl_verify($Xi['version'], base64_decode($Xi['signature']), $tg) == 1) {
            $_COOKIE['adminer_version'] = $Xi['version'];
        }
    }echo script('mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick'.(isset($_COOKIE['adminer_version']) ? '' : ", onload: partial(verifyVersion, '$ia', '".js_escape(ME)."', '".get_token()."')")."});
document.body.className = document.body.className.replace(/ nojs/, ' js');
var offlineMessage = '".js_escape(lang(80))."';
var thousandsSeparator = '".js_escape(lang(4))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: function () { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n";
    if ($Na !== null) {
        $A = substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1);
        echo '<p id="breadcrumb"><a href="'.h($A ?: '.').'">'.$bc[DRIVER].'</a> » ';
        $A = substr(preg_replace('~\b(db|ns)=[^&]*&~', '', ME), 0, -1);
        $N = $b->serverName(SERVER);
        $N = ($N != '' ? $N : lang(33));
        if ($Na === false) {
            echo "$N\n";
        } else {
            echo "<a href='".h($A)."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";
            if ($_GET['ns'] != '' || (DB != '' && is_array($Na))) {
                echo '<a href="'.h($A.'&db='.urlencode(DB).(support('scheme') ? '&ns=' : '')).'">'.h(DB).'</a> » ';
            }if (is_array($Na)) {
                if ($_GET['ns'] != '') {
                    echo '<a href="'.h(substr(ME, 0, -1)).'">'.h($_GET['ns']).'</a> » ';
                }foreach ($Na as $z => $X) {
                    $Tb = (is_array($X) ? $X[1] : h($X));
                    if ($Tb != '') {
                        echo "<a href='".h(ME."$z=").urlencode(is_array($X) ? $X[0] : $X)."'>$Tb</a> » ";
                    }
                }
            }echo "$gi\n";
        }
    }echo "<h2>$ii</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";
    restart_session();
    page_messages($n);
    $j = &get_session('dbs');
    if (DB != '' && $j && ! in_array(DB, $j, true)) {
        $j = null;
    }stop_session();
    define('Adminer\PAGE_HEADER', 1);
}function page_headers()
{
    global $b;
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-cache');
    header('X-Frame-Options: deny');
    header('X-XSS-Protection: 0');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: origin-when-cross-origin');
    foreach ($b->csp() as $Db) {
        $zd = [];
        foreach ($Db as $z => $X) {
            $zd[] = "$z $X";
        }header('Content-Security-Policy: '.implode('; ', $zd));
    }$b->headers();
}function csp()
{
    return [['script-src' => "'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'", 'connect-src' => "'self'", 'frame-src' => 'https://www.adminer.org', 'object-src' => "'none'", 'base-uri' => "'none'", 'form-action' => "'self'"]];
}function get_nonce()
{
    static $df;
    if (! $df) {
        $df = base64_encode(rand_string());
    }

return $df;
}function page_messages($n)
{
    $Ki = preg_replace('~^[^?]*~', '', $_SERVER['REQUEST_URI']);
    $Qe = $_SESSION['messages'][$Ki];
    if ($Qe) {
        echo "<div class='message'>".implode("</div>\n<div class='message'>", $Qe).'</div>'.script('messagesPrint();');
        unset($_SESSION['messages'][$Ki]);
    }if ($n) {
        echo "<div class='error'>$n</div>\n";
    }
}function page_footer($Se = '')
{
    global $b,$mi;
    echo '</div>

<div id="menu">
';
    $b->navigation($Se);
    echo '</div>

';
    if ($Se != 'auth') {
        echo '<form action="" method="post">
<p class="logout">
<span>',h($_GET['username'])."\n",'</span>
<input type="submit" name="logout" value="',lang(81),'" id="logout">
<input type="hidden" name="token" value="',$mi,'">
</p>
</form>
';
    }echo script('setupSubmitHighlight(document);');
}function int32($We)
{
    while ($We >= 2147483648) {
        $We -= 4294967296;
    }while ($We <= -2147483649) {
        $We += 4294967296;
    }

return (int) $We;
}function long2str($W, $bj)
{
    $Tg = '';
    foreach ($W as $X) {
        $Tg .= pack('V', $X);
    }if ($bj) {
        return substr($Tg, 0, end($W));
    }

return $Tg;
}function str2long($Tg, $bj)
{
    $W = array_values(unpack('V*', str_pad($Tg, 4 * ceil(strlen($Tg) / 4), "\0")));
    if ($bj) {
        $W[] = strlen($Tg);
    }

return $W;
}function xxtea_mx($ij, $hj, $Jh, $ee)
{
    return int32((($ij >> 5 & 0x7FFFFFF) ^ $hj << 2) + (($hj >> 3 & 0x1FFFFFFF) ^ $ij << 4)) ^ int32(($Jh ^ $hj) + ($ee ^ $ij));
}function encrypt_string($Eh, $z)
{
    if ($Eh == '') {
        return '';
    }$z = array_values(unpack('V*', pack('H*', md5($z))));
    $W = str2long($Eh, true);
    $We = count($W) - 1;
    $ij = $W[$We];
    $hj = $W[0];
    $ug = floor(6 + 52 / ($We + 1));
    $Jh = 0;
    while ($ug-- > 0) {
        $Jh = int32($Jh + 0x9E3779B9);
        $jc = $Jh >> 2 & 3;
        for ($Of = 0; $Of < $We; $Of++) {
            $hj = $W[$Of + 1];
            $Ve = xxtea_mx($ij, $hj, $Jh, $z[$Of & 3 ^ $jc]);
            $ij = int32($W[$Of] + $Ve);
            $W[$Of] = $ij;
        }$hj = $W[0];
        $Ve = xxtea_mx($ij, $hj, $Jh, $z[$Of & 3 ^ $jc]);
        $ij = int32($W[$We] + $Ve);
        $W[$We] = $ij;
    }

return long2str($W, false);
}function decrypt_string($Eh, $z)
{
    if ($Eh == '') {
        return '';
    }if (! $z) {
        return false;
    }$z = array_values(unpack('V*', pack('H*', md5($z))));
    $W = str2long($Eh, false);
    $We = count($W) - 1;
    $ij = $W[$We];
    $hj = $W[0];
    $ug = floor(6 + 52 / ($We + 1));
    $Jh = int32($ug * 0x9E3779B9);
    while ($Jh) {
        $jc = $Jh >> 2 & 3;
        for ($Of = $We; $Of > 0; $Of--) {
            $ij = $W[$Of - 1];
            $Ve = xxtea_mx($ij, $hj, $Jh, $z[$Of & 3 ^ $jc]);
            $hj = int32($W[$Of] - $Ve);
            $W[$Of] = $hj;
        }$ij = $W[$We];
        $Ve = xxtea_mx($ij, $hj, $Jh, $z[$Of & 3 ^ $jc]);
        $hj = int32($W[0] - $Ve);
        $W[0] = $hj;
        $Jh = int32($Jh - 0x9E3779B9);
    }

return long2str($W, true);
}$g = '';
$yd = $_SESSION['token'];
if (! $yd) {
    $_SESSION['token'] = rand(1, 1e6);
}$mi = get_token();
$cg = [];
if ($_COOKIE['adminer_permanent']) {
    foreach (explode(' ', $_COOKIE['adminer_permanent']) as $X) {
        [$z] = explode(':', $X);
        $cg[$z] = $X;
    }
}function add_invalid_login()
{
    global $b;
    $Ha = get_temp_dir().'/adminer.invalid';
    foreach (glob("$Ha*") ?: [$Ha] as $q) {
        $s = file_open_lock($q);
        if ($s) {
            break;
        }
    }if (! $s) {
        $s = file_open_lock("$Ha-".rand_string());
    }if (! $s) {
        return;
    }$Wd = unserialize(stream_get_contents($s));
    $di = time();
    if ($Wd) {
        foreach ($Wd as $Xd => $X) {
            if ($X[0] < $di) {
                unset($Wd[$Xd]);
            }
        }
    }$Vd = &$Wd[$b->bruteForceKey()];
    if (! $Vd) {
        $Vd = [$di + 30 * 60, 0];
    }$Vd[1]++;
    file_write_unlock($s, serialize($Wd));
}function check_invalid_login()
{
    global $b;
    $Wd = [];
    foreach (glob(get_temp_dir().'/adminer.invalid*') as $q) {
        $s = file_open_lock($q);
        if ($s) {
            $Wd = unserialize(stream_get_contents($s));
            file_unlock($s);
            break;
        }
    }$Vd = ($Wd ? $Wd[$b->bruteForceKey()] : []);
    $cf = ($Vd[1] > 29 ? $Vd[0] - time() : 0);
    if ($cf > 0) {
        auth_error(lang(82, ceil($cf / 60)));
    }
}$Aa = $_POST['auth'];
if ($Aa) {
    session_regenerate_id();
    $Wi = $Aa['driver'];
    $N = $Aa['server'];
    $V = $Aa['username'];
    $F = (string) $Aa['password'];
    $k = $Aa['db'];
    set_password($Wi, $N, $V, $F);
    $_SESSION['db'][$Wi][$N][$V][$k] = true;
    if ($Aa['permanent']) {
        $z = implode('-', array_map('base64_encode', [$Wi, $N, $V, $k]));
        $og = $b->permanentLogin(true);
        $cg[$z] = "$z:".base64_encode($og ? encrypt_string($F, $og) : '');
        cookie('adminer_permanent', implode(' ', $cg));
    }if (count($_POST) == 1 || $Wi != DRIVER || $N != SERVER || $_GET['username'] !== $V || $k != DB) {
        redirect(auth_url($Wi, $N, $V, $k));
    }
} elseif ($_POST['logout'] && (! $yd || verify_token())) {
    foreach (['pwds', 'db', 'dbs', 'queries'] as $z) {
        set_session($z, null);
    }unset_permanent();
    redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1), lang(83).' '.lang(84));
} elseif ($cg && ! $_SESSION['pwds']) {
    session_regenerate_id();
    $og = $b->permanentLogin();
    foreach ($cg as $z => $X) {
        [, $bb] = explode(':', $X);
        [$Wi, $N, $V, $k] = array_map('base64_decode', explode('-', $z));
        set_password($Wi, $N, $V, decrypt_string(base64_decode($bb), $og));
        $_SESSION['db'][$Wi][$N][$V][$k] = true;
    }
}function unset_permanent()
{
    global $cg;
    foreach ($cg as $z => $X) {
        [$Wi, $N, $V, $k] = array_map('base64_decode', explode('-', $z));
        if ($Wi == DRIVER && $N == SERVER && $V == $_GET['username'] && $k == DB) {
            unset($cg[$z]);
        }
    }cookie('adminer_permanent', implode(' ', $cg));
}function auth_error($n)
{
    global $b,$yd;
    $lh = session_name();
    if (isset($_GET['username'])) {
        header('HTTP/1.1 403 Forbidden');
        if (($_COOKIE[$lh] || $_GET[$lh]) && ! $yd) {
            $n = lang(85);
        } else {
            restart_session();
            add_invalid_login();
            $F = get_password();
            if ($F !== null) {
                if ($F === false) {
                    $n .= ($n ? '<br>' : '').lang(86, target_blank(), '<code>permanentLogin()</code>');
                }set_password(DRIVER, SERVER, $_GET['username'], null);
            }unset_permanent();
        }
    }if (! $_COOKIE[$lh] && $_GET[$lh] && ini_bool('session.use_only_cookies')) {
        $n = lang(87);
    }$Rf = session_get_cookie_params();
    cookie('adminer_key', ($_COOKIE['adminer_key'] ?: rand_string()), $Rf['lifetime']);
    page_header(lang(37), $n, null);
    echo "<form action='' method='post'>\n",'<div>';
    if (hidden_fields($_POST, ['auth'])) {
        echo "<p class='message'>".lang(88)."\n";
    }echo "</div>\n";
    $b->loginForm();
    echo "</form>\n";
    page_footer('auth');
    exit;
}if (isset($_GET['username']) && ! class_exists('Adminer\Db')) {
    unset($_SESSION['pwds'][DRIVER]);
    unset_permanent();
    page_header(lang(89), lang(90, implode(', ', Driver::$ig)), false);
    page_footer('auth');
    exit;
}stop_session(true);
if (isset($_GET['username']) && is_string(get_password())) {
    [$Cd, $eg] = explode(':', SERVER, 2);
    if (preg_match('~^\s*([-+]?\d+)~', $eg, $B) && ($B[1] < 1024 || $B[1] > 65535)) {
        auth_error(lang(91));
    }check_invalid_login();
    $g = connect($b->credentials());
    if (is_object($g)) {
        $m = new Driver($g);
        if ($b->operators === null) {
            $b->operators = $m->operators;
        }if (isset($g->maria) || $g->cockroach) {
            save_settings(['vendor-'.SERVER => $bc[DRIVER]]);
        }
    }
}$ye = null;
if (! is_object($g) || ($ye = $b->login($_GET['username'], get_password())) !== true) {
    $n = (is_string($g) ? nl_br(h($g)) : (is_string($ye) ? $ye : lang(92)));
    auth_error($n.(preg_match('~^ | $~', get_password()) ? '<br>'.lang(93) : ''));
}if ($_POST['logout'] && $yd && ! verify_token()) {
    page_header(lang(81), lang(94));
    page_footer('db');
    exit;
}if ($Aa && $_POST['token']) {
    $_POST['token'] = $mi;
}$n = '';
if ($_POST) {
    if (! verify_token()) {
        $Qd = 'max_input_vars';
        $Je = ini_get($Qd);
        if (extension_loaded('suhosin')) {
            foreach (['suhosin.request.max_vars', 'suhosin.post.max_vars'] as $z) {
                $X = ini_get($z);
                if ($X && (! $Je || $X < $Je)) {
                    $Qd = $z;
                    $Je = $X;
                }
            }
        }$n = (! $_POST['token'] && $Je ? lang(95, "'$Qd'") : lang(94).' '.lang(96));
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $n = lang(97, "'post_max_size'");
    if (isset($_GET['sql'])) {
        $n .= ' '.lang(98);
    }
}function select($I, $h = null, $Df = [], $_ = 0)
{
    $we = [];
    $y = [];
    $e = [];
    $La = [];
    $Ai = [];
    $J = [];
    for ($u = 0; (! $_ || $u < $_) && ($K = $I->fetch_row()); $u++) {
        if (! $u) {
            echo "<div class='scrollable'>\n","<table class='nowrap odds'>\n",'<thead><tr>';
            for ($ce = 0; $ce < count($K); $ce++) {
                $o = $I->fetch_field();
                $C = $o->name;
                $Cf = $o->orgtable;
                $Bf = $o->orgname;
                $J[$o->table] = $Cf;
                if ($Df && JUSH == 'sql') {
                    $we[$ce] = ($C == 'table' ? 'table=' : ($C == 'possible_keys' ? 'indexes=' : null));
                } elseif ($Cf != '') {
                    if (! isset($y[$Cf])) {
                        $y[$Cf] = [];
                        foreach (indexes($Cf, $h) as $x) {
                            if ($x['type'] == 'PRIMARY') {
                                $y[$Cf] = array_flip($x['columns']);
                                break;
                            }
                        }$e[$Cf] = $y[$Cf];
                    }if (isset($e[$Cf][$Bf])) {
                        unset($e[$Cf][$Bf]);
                        $y[$Cf][$Bf] = $ce;
                        $we[$ce] = $Cf;
                    }
                }if ($o->charsetnr == 63) {
                    $La[$ce] = true;
                }$Ai[$ce] = $o->type;
                echo '<th'.($Cf != '' || $o->name != $Bf ? " title='".h(($Cf != '' ? "$Cf." : '').$Bf)."'" : '').'>'.h($C).($Df ? doc_link(['sql' => 'explain-output.html#explain_'.strtolower($C), 'mariadb' => 'explain/#the-columns-in-explain-select']) : '');
            }echo "</thead>\n";
        }echo '<tr>';
        foreach ($K as $z => $X) {
            $A = '';
            if (isset($we[$z]) && ! $e[$we[$z]]) {
                if ($Df && JUSH == 'sql') {
                    $R = $K[array_search('table=', $we)];
                    $A = ME.$we[$z].urlencode($Df[$R] != '' ? $Df[$R] : $R);
                } else {
                    $A = ME.'edit='.urlencode($we[$z]);
                    foreach ($y[$we[$z]] as $fb => $ce) {
                        $A .= '&where'.urlencode('['.bracket_escape($fb).']').'='.urlencode($K[$ce]);
                    }
                }
            } elseif (is_url($X)) {
                $A = $X;
            }if ($X === null) {
                $X = '<i>NULL</i>';
            } elseif ($La[$z] && ! is_utf8($X)) {
                $X = '<i>'.lang(46, strlen($X)).'</i>';
            } else {
                $X = h($X);
                if ($Ai[$z] == 254) {
                    $X = "<code>$X</code>";
                }
            }if ($A) {
                $X = "<a href='".h($A)."'".(is_url($A) ? target_blank() : '').">$X</a>";
            }echo '<td'.($Ai[$z] <= 9 || $Ai[$z] == 246 ? " class='number'" : '').">$X";
        }
    }echo ($u ? "</table>\n</div>" : "<p class='message'>".lang(12))."\n";

    return $J;
}function referencable_primary($dh)
{
    $J = [];
    foreach (table_status('', true) as $Oh => $R) {
        if ($Oh != $dh && fk_support($R)) {
            foreach (fields($Oh) as $o) {
                if ($o['primary']) {
                    if ($J[$Oh]) {
                        unset($J[$Oh]);
                        break;
                    }$J[$Oh] = $o;
                }
            }
        }
    }

return $J;
}function textarea($C, $Y, $L = 10, $jb = 80)
{
    echo "<textarea name='".h($C)."' rows='$L' cols='$jb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";
    if (is_array($Y)) {
        foreach ($Y as $X) {
            echo h($X[0])."\n\n\n";
        }
    } else {
        echo h($Y);
    }echo '</textarea>';
}function select_input($_a, $yf, $Y = '', $sf = '', $dg = '')
{
    $Vh = ($yf ? 'select' : 'input');

    return "<$Vh$_a".($yf ? "><option value=''>$dg".optionlist($yf, $Y, true).'</select>' : " size='10' value='".h($Y)."' placeholder='$dg'>").($sf ? script("qsl('$Vh').onchange = $sf;", '') : '');
}function json_row($z, $X = null)
{
    static $Vc = true;
    if ($Vc) {
        echo '{';
    }if ($z != '') {
        echo ($Vc ? '' : ',')."\n\t\"".addcslashes($z, "\r\n\t\"\\/").'": '.($X !== null ? '"'.addcslashes($X, "\r\n\"\\/").'"' : 'null');
        $Vc = false;
    } else {
        echo "\n}\n";
        $Vc = true;
    }
}function edit_type($z, $o, $ib, $ed = [], $Mc = [])
{
    global $m;
    $U = $o['type'];
    echo "<td><select name='".h($z)."[type]' class='type' aria-labelledby='label-type'>";
    if ($U && ! array_key_exists($U, $m->types()) && ! isset($ed[$U]) && ! in_array($U, $Mc)) {
        $Mc[] = $U;
    }$Fh = $m->structuredTypes();
    if ($ed) {
        $Fh[lang(99)] = $ed;
    }echo optionlist(array_merge($Mc, $Fh), $U),'</select><td>',"<input name='".h($z)."[length]' value='".h($o['length'])."' size='3'".(! $o['length'] && preg_match('~var(char|binary)$~', $U) ? " class='required'" : '')." aria-labelledby='label-length'>","<td class='options'>",($ib ? "<input list='collations' name='".h($z)."[collation]'".(preg_match('~(char|text|enum|set)$~', $U) ? '' : " class='hidden'")." value='".h($o['collation'])."' placeholder='(".lang(100).")'>" : ''),($m->unsigned ? "<select name='".h($z)."[unsigned]'".(! $U || preg_match(number_type(), $U) ? '' : " class='hidden'").'><option>'.optionlist($m->unsigned, $o['unsigned']).'</select>' : ''),(isset($o['on_update']) ? "<select name='".h($z)."[on_update]'".(preg_match('~timestamp|datetime~', $U) ? '' : " class='hidden'").'>'.optionlist(['' => '('.lang(101).')', 'CURRENT_TIMESTAMP'], (preg_match('~^CURRENT_TIMESTAMP~i', $o['on_update']) ? 'CURRENT_TIMESTAMP' : $o['on_update'])).'</select>' : ''),($ed ? "<select name='".h($z)."[on_delete]'".(preg_match('~`~', $U) ? '' : " class='hidden'")."><option value=''>(".lang(102).')'.optionlist(explode('|', $m->onActions), $o['on_delete']).'</select> ' : ' ');
}function get_partitions_info($R)
{
    global $g;
    $id = 'FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = '.q(DB).' AND TABLE_NAME = '.q($R);
    $I = $g->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $id ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");
    $J = [];
    [$J['partition_by'], $J['partition'], $J['partitions']] = $I->fetch_row();
    $Xf = get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $id AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");
    $J['partition_names'] = array_keys($Xf);
    $J['partition_values'] = array_values($Xf);

    return $J;
}function process_length($te)
{
    global $m;
    $wc = $m->enumLength;

    return preg_match("~^\\s*\\(?\\s*$wc(?:\\s*,\\s*$wc)*+\\s*\\)?\\s*\$~", $te) && preg_match_all("~$wc~", $te, $De) ? '('.implode(',', $De[0]).')' : preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $te));
}function process_type($o, $gb = 'COLLATE')
{
    global $m;

    return " $o[type]".process_length($o['length']).(preg_match(number_type(), $o['type']) && in_array($o['unsigned'], $m->unsigned) ? " $o[unsigned]" : '').(preg_match('~char|text|enum|set~', $o['type']) && $o['collation'] ? " $gb ".(JUSH == 'mssql' ? $o['collation'] : q($o['collation'])) : '');
}function process_field($o, $zi)
{
    if ($o['on_update']) {
        $o['on_update'] = str_ireplace('current_timestamp()', 'CURRENT_TIMESTAMP', $o['on_update']);
    }

return [idf_escape(trim($o['field'])), process_type($zi), ($o['null'] ? ' NULL' : ' NOT NULL'), default_value($o), (preg_match('~timestamp|datetime~', $o['type']) && $o['on_update'] ? " ON UPDATE $o[on_update]" : ''), (support('comment') && $o['comment'] != '' ? ' COMMENT '.q($o['comment']) : ''), ($o['auto_increment'] ? auto_increment() : null)];
}function default_value($o)
{
    global $m;
    $l = $o['default'];
    $ld = $o['generated'];

    return $l === null ? '' : (in_array($ld, $m->generated) ? (JUSH == 'mssql' ? " AS ($l)".($ld == 'VIRTUAL' ? '' : " $ld").'' : " GENERATED ALWAYS AS ($l) $ld") : ' DEFAULT '.(! preg_match('~^GENERATED ~i', $l) && (preg_match('~char|binary|text|json|enum|set~', $o['type']) || preg_match('~^(?![a-z])~i', $l)) ? (JUSH == 'sql' && preg_match('~text|json~', $o['type']) ? '('.q($l).')' : q($l)) : str_ireplace('current_timestamp()', 'CURRENT_TIMESTAMP', (JUSH == 'sqlite' ? "($l)" : $l))));
}function type_class($U)
{
    foreach (['char' => 'text', 'date' => 'time|year', 'binary' => 'blob', 'enum' => 'set'] as $z => $X) {
        if (preg_match("~$z|$X~", $U)) {
            return " class='$z'";
        }
    }
}function edit_fields($p, $ib, $U = 'TABLE', $ed = [])
{
    global $m;
    $p = array_values($p);
    $Pb = (($_POST ? $_POST['defaults'] : get_setting('defaults')) ? '' : " class='hidden'");
    $ob = (($_POST ? $_POST['comments'] : get_setting('comments')) ? '' : " class='hidden'");
    echo '<thead><tr>
',($U == 'PROCEDURE' ? '<td>' : ''),'<th id="label-name">',($U == 'TABLE' ? lang(103) : lang(104)),'<td id="label-type">',lang(48),'<textarea id="enum-edit" rows="4" cols="12" wrap="off" style="display: none;"></textarea>',script("qs('#enum-edit').onblur = editingLengthBlur;"),'<td id="label-length">',lang(105),'<td>',lang(106);
    if ($U == 'TABLE') {
        echo "<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(50)."'>AI</abbr>",doc_link(['sql' => 'example-auto-increment.html', 'mariadb' => 'auto_increment/', 'sqlite' => 'autoinc.html', 'pgsql' => 'datatype-numeric.html#DATATYPE-SERIAL', 'mssql' => 't-sql/statements/create-table-transact-sql-identity-property']),"<td id='label-default'$Pb>".lang(51),(support('comment') ? "<td id='label-comment'$ob>".lang(49) : '');
    }echo "<td><input type='image' class='icon' name='add[".(support('move_col') ? 0 : count($p))."]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=plus.gif&version=5.0.6')."' alt='+' title='".lang(107)."'>".script('row_count = '.count($p).';'),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");
    foreach ($p as $u => $o) {
        $u++;
        $Ef = $o[($_POST ? 'orig' : 'field')];
        $Yb = (isset($_POST['add'][$u - 1]) || (isset($o['field']) && ! $_POST['drop_col'][$u])) && (support('drop_col') || $Ef == '');
        echo '<tr'.($Yb ? '' : " style='display: none;'").">\n",($U == 'PROCEDURE' ? '<td>'.html_select("fields[$u][inout]", explode('|', $m->inout), $o['inout']) : '').'<th>';
        if ($Yb) {
            echo "<input name='fields[$u][field]' value='".h($o['field'])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'>\n";
        }echo "<input type='hidden' name='fields[$u][orig]' value='".h($Ef)."'>";
        edit_type("fields[$u]", $o, $ib, $ed);
        if ($U == 'TABLE') {
            echo '<td>'.checkbox("fields[$u][null]", 1, $o['null'], '', '', 'block', 'label-null'),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$u'".($o['auto_increment'] ? ' checked' : '')." aria-labelledby='label-ai'></label>","<td$Pb>".($m->generated ? html_select("fields[$u][generated]", array_merge(['', 'DEFAULT'], $m->generated), $o['generated']).' ' : checkbox("fields[$u][generated]", 1, $o['generated'], '', '', '', 'label-default')),"<input name='fields[$u][default]' value='".h($o['default'])."' aria-labelledby='label-default'>",(support('comment') ? "<td$ob><input name='fields[$u][comment]' value='".h($o['comment'])."' data-maxlength='".(min_version(5.5) ? 1024 : 255)."' aria-labelledby='label-comment'>" : '');
        }echo '<td>',(support('move_col') ? "<input type='image' class='icon' name='add[$u]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=plus.gif&version=5.0.6')."' alt='+' title='".lang(107)."'> "."<input type='image' class='icon' name='up[$u]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=up.gif&version=5.0.6')."' alt='↑' title='".lang(108)."'> "."<input type='image' class='icon' name='down[$u]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=down.gif&version=5.0.6')."' alt='↓' title='".lang(109)."'> " : ''),($Ef == '' || support('drop_col') ? "<input type='image' class='icon' name='drop_col[$u]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=cross.gif&version=5.0.6')."' alt='x' title='".lang(110)."'>" : '');
    }
}function process_fields(&$p)
{
    $D = 0;
    if ($_POST['up']) {
        $ne = 0;
        foreach ($p as $z => $o) {
            if (key($_POST['up']) == $z) {
                unset($p[$z]);
                array_splice($p, $ne, 0, [$o]);
                break;
            }if (isset($o['field'])) {
                $ne = $D;
            }$D++;
        }
    } elseif ($_POST['down']) {
        $gd = false;
        foreach ($p as $z => $o) {
            if (isset($o['field']) && $gd) {
                unset($p[key($_POST['down'])]);
                array_splice($p, $D, 0, [$gd]);
                break;
            }if (key($_POST['down']) == $z) {
                $gd = $o;
            }$D++;
        }
    } elseif ($_POST['add']) {
        $p = array_values($p);
        array_splice($p, key($_POST['add']), 0, [[]]);
    } elseif (! $_POST['drop_col']) {
        return false;
    }

return true;
}function normalize_enum($B)
{
    return "'".str_replace("'", "''", addcslashes(stripcslashes(str_replace($B[0][0].$B[0][0], $B[0][0], substr($B[0], 1, -1))), '\\'))."'";
}function grant($nd, $qg, $e, $pf)
{
    if (! $qg) {
        return true;
    }if ($qg == ['ALL PRIVILEGES', 'GRANT OPTION']) {
        return $nd == 'GRANT' ? queries("$nd ALL PRIVILEGES$pf WITH GRANT OPTION") : queries("$nd ALL PRIVILEGES$pf") && queries("$nd GRANT OPTION$pf");
    }

return queries("$nd ".preg_replace('~(GRANT OPTION)\([^)]*\)~', '\1', implode("$e, ", $qg).$e).$pf);
}function drop_create($cc, $i, $ec, $Zh, $gc, $xe, $Pe, $Ne, $Oe, $mf, $af)
{
    if ($_POST['drop']) {
        query_redirect($cc, $xe, $Pe);
    } elseif ($mf == '') {
        query_redirect($i, $xe, $Oe);
    } elseif ($mf != $af) {
        $Bb = queries($i);
        queries_redirect($xe, $Ne, $Bb && queries($cc));
        if ($Bb) {
            queries($ec);
        }
    } else {
        queries_redirect($xe, $Ne, queries($Zh) && queries($gc) && queries($cc) && queries($i));
    }
}function create_trigger($pf, $K)
{
    $fi = " $K[Timing] $K[Event]".(preg_match('~ OF~', $K['Event']) ? " $K[Of]" : '');

    return 'CREATE TRIGGER '.idf_escape($K['Trigger']).(JUSH == 'mssql' ? $pf.$fi : $fi.$pf).rtrim(" $K[Type]\n$K[Statement]", ';').';';
}function create_routine($Pg, $K)
{
    global $m;
    $O = [];
    $p = (array) $K['fields'];
    ksort($p);
    foreach ($p as $o) {
        if ($o['field'] != '') {
            $O[] = (preg_match("~^($m->inout)\$~", $o['inout']) ? "$o[inout] " : '').idf_escape($o['field']).process_type($o, 'CHARACTER SET');
        }
    }$Qb = rtrim($K['definition'], ';');

    return "CREATE $Pg ".idf_escape(trim($K['name'])).' ('.implode(', ', $O).')'.($Pg == 'FUNCTION' ? ' RETURNS'.process_type($K['returns'], 'CHARACTER SET') : '').($K['language'] ? " LANGUAGE $K[language]" : '').(JUSH == 'pgsql' ? ' AS '.q($Qb) : "\n$Qb;");
}function remove_definer($H)
{
    return preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~', '`@`(%|\1)', logged_user()).'`~', '\1', $H);
}function format_foreign_key($r)
{
    global $m;
    $k = $r['db'];
    $ef = $r['ns'];

    return ' FOREIGN KEY ('.implode(', ', array_map('Adminer\idf_escape', $r['source'])).') REFERENCES '.($k != '' && $k != $_GET['db'] ? idf_escape($k).'.' : '').($ef != '' && $ef != $_GET['ns'] ? idf_escape($ef).'.' : '').idf_escape($r['table']).' ('.implode(', ', array_map('Adminer\idf_escape', $r['target'])).')'.(preg_match("~^($m->onActions)\$~", $r['on_delete']) ? " ON DELETE $r[on_delete]" : '').(preg_match("~^($m->onActions)\$~", $r['on_update']) ? " ON UPDATE $r[on_update]" : '');
}function tar_file($q, $ki)
{
    $J = pack('a100a8a8a8a12a12', $q, 644, 0, 0, decoct($ki->size), decoct(time()));
    $ab = 8 * 32;
    for ($u = 0; $u < strlen($J); $u++) {
        $ab += ord($J[$u]);
    }$J .= sprintf('%06o', $ab)."\0 ";
    echo $J,str_repeat("\0", 512 - strlen($J));
    $ki->send();
    echo str_repeat("\0", 511 - ($ki->size + 511) % 512);
}function ini_bytes($Qd)
{
    $X = ini_get($Qd);
    switch (strtolower(substr($X, -1))) {
        case 'g':$X = (int) $X * 1024;
        case 'm':$X = (int) $X * 1024;
        case 'k':$X = (int) $X * 1024;
    }

return $X;
}function doc_link($Zf, $ai = '<sup>?</sup>')
{
    global $g;
    $jh = $g->server_info;
    $Xi = preg_replace('~^(\d\.?\d).*~s', '\1', $jh);
    $Mi = ['sql' => "https://dev.mysql.com/doc/refman/$Xi/en/", 'sqlite' => 'https://www.sqlite.org/', 'pgsql' => "https://www.postgresql.org/docs/$Xi/", 'mssql' => 'https://learn.microsoft.com/en-us/sql/', 'oracle' => 'https://www.oracle.com/pls/topic/lookup?ctx=db'.preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s', '\1\2', $jh).'&id='];
    if ($g->maria) {
        $Mi['sql'] = 'https://mariadb.com/kb/en/';
        $Zf['sql'] = (isset($Zf['mariadb']) ? $Zf['mariadb'] : str_replace('.html', '/', $Zf['sql']));
    }

return $Zf[JUSH] ? "<a href='".h($Mi[JUSH].$Zf[JUSH].(JUSH == 'mssql' ? "?view=sql-server-ver$Xi" : ''))."'".target_blank().">$ai</a>" : '';
}function db_size($k)
{
    global $g;
    if (! $g->select_db($k)) {
        return '?';
    }$J = 0;
    foreach (table_status() as $S) {
        $J += $S['Data_length'] + $S['Index_length'];
    }

return format_number($J);
}function set_utf8mb4($i)
{
    global $g;
    static $O = false;
    if (! $O && preg_match('~\butf8mb4~i', $i)) {
        $O = true;
        echo 'SET NAMES '.charset($g).";\n\n";
    }
}if (isset($_GET['status'])) {
    $_GET['variables'] = $_GET['status'];
}if (isset($_GET['import'])) {
    $_GET['sql'] = $_GET['import'];
}if (! (DB != '' ? $g->select_db(DB) : isset($_GET['sql']) || isset($_GET['dump']) || isset($_GET['database']) || isset($_GET['processlist']) || isset($_GET['privileges']) || isset($_GET['user']) || isset($_GET['variables']) || $_GET['script'] == 'connect' || $_GET['script'] == 'kill')) {
    if (DB != '' || $_GET['refresh']) {
        restart_session();
        set_session('dbs', null);
    }if (DB != '') {
        header('HTTP/1.1 404 Not Found');
        page_header(lang(36).': '.h(DB), lang(111), true);
    } else {
        if ($_POST['db'] && ! $n) {
            queries_redirect(substr(ME, 0, -1), lang(112), drop_databases($_POST['db']));
        }page_header(lang(113), $n, false);
        echo "<p class='links'>\n";
        foreach (['database' => lang(114), 'privileges' => lang(70), 'processlist' => lang(115), 'variables' => lang(116), 'status' => lang(117)] as $z => $X) {
            if (support($z)) {
                echo "<a href='".h(ME)."$z='>$X</a>\n";
            }
        }echo '<p>'.lang(118, $bc[DRIVER], '<b>'.h($g->server_info).'</b>', "<b>$g->extension</b>")."\n",'<p>'.lang(119, '<b>'.h(logged_user()).'</b>')."\n";
        $j = $b->databases();
        if ($j) {
            $Xg = support('scheme');
            $ib = collations();
            echo "<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr>'.(support('database') ? '<td>' : '').'<th>'.lang(36).(get_session('dbs') !== null ? " - <a href='".h(ME)."refresh=1'>".lang(120).'</a>' : '').'<td>'.lang(121).'<td>'.lang(122).'<td>'.lang(123)." - <a href='".h(ME)."dbsize=1'>".lang(124).'</a>'.script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');", '')."</thead>\n";
            $j = ($_GET['dbsize'] ? count_tables($j) : array_flip($j));
            foreach ($j as $k => $T) {
                $Og = h(ME).'db='.urlencode($k);
                $v = h('Db-'.$k);
                echo '<tr>'.(support('database') ? '<td>'.checkbox('db[]', $k, in_array($k, (array) $_POST['db']), '', '', '', $v) : ''),"<th><a href='$Og' id='$v'>".h($k).'</a>';
                $hb = h(db_collation($k, $ib));
                echo '<td>'.(support('database') ? "<a href='$Og".($Xg ? '&amp;ns=' : '')."&amp;database=' title='".lang(66)."'>$hb</a>" : $hb),"<td align='right'><a href='$Og&amp;schema=' id='tables-".h($k)."' title='".lang(69)."'>".($_GET['dbsize'] ? $T : '?').'</a>',"<td align='right' id='size-".h($k)."'>".($_GET['dbsize'] ? db_size($k) : '?'),"\n";
            }echo "</table>\n",(support('database') ? "<div class='footer'><div>\n".'<fieldset><legend>'.lang(125)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''>".script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".lang(126)."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n" : ''),"<input type='hidden' name='token' value='$mi'>\n","</form>\n",script('tableCheck();');
        }
    }page_footer('db');
    exit;
}if (support('scheme')) {
    if (DB != '' && $_GET['ns'] !== '') {
        if (! isset($_GET['ns'])) {
            redirect(preg_replace('~ns=[^&]*&~', '', ME).'ns='.get_schema());
        }if (! set_schema($_GET['ns'])) {
            header('HTTP/1.1 404 Not Found');
            page_header(lang(75).': '.h($_GET['ns']), lang(127), true);
            page_footer('ns');
            exit;
        }
    }
}class TmpFile
{
    private $handler;

    public $sizeprivate;

    public function __construct()
    {
        $this->handler = tmpfile();
    }

    public function write($wb)
    {
        $this->size += strlen($wb);
        fwrite($this->handler, $wb);
    }

    public function send()
    {
        fseek($this->handler, 0);
        fpassthru($this->handler);
        fclose($this->handler);
    }
}if (isset($_GET['select']) && ($_POST['edit'] || $_POST['clone']) && ! $_POST['save']) {
    $_GET['edit'] = $_GET['select'];
}if (isset($_GET['callf'])) {
    $_GET['call'] = $_GET['callf'];
}if (isset($_GET['function'])) {
    $_GET['procedure'] = $_GET['function'];
}if (isset($_GET['download'])) {
    $a = $_GET['download'];
    $p = fields($a);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.friendly_url("$a-".implode('_', $_GET['where'])).'.'.friendly_url($_GET['field']));
    $M = [idf_escape($_GET['field'])];
    $I = $m->select($a, $M, [where($_GET, $p)], $M);
    $K = ($I ? $I->fetch_row() : []);
    echo $m->value($K[0], $p[$_GET['field']]);
    exit;
} elseif (isset($_GET['table'])) {
    $a = $_GET['table'];
    $p = fields($a);
    if (! $p) {
        $n = error();
    }$S = table_status1($a, true);
    $C = $b->tableName($S);
    page_header(($p && is_view($S) ? $S['Engine'] == 'materialized view' ? lang(128) : lang(129) : lang(130)).': '.($C != '' ? $C : h($a)), $n);
    $Ng = [];
    foreach ($p as $z => $o) {
        $Ng += $o['privileges'];
    }$b->selectLinks($S, (isset($Ng['insert']) || ! support('table') ? '' : null));
    $nb = $S['Comment'];
    if ($nb != '') {
        echo "<p class='nowrap'>".lang(49).': '.h($nb)."\n";
    }if ($p) {
        $b->tableStructurePrint($p);
    }if (support('indexes') && $m->supportsIndex($S)) {
        echo "<h3 id='indexes'>".lang(131)."</h3>\n";
        $y = indexes($a);
        if ($y) {
            $b->tableIndexesPrint($y);
        }echo '<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.lang(132)."</a>\n";
    }if (! is_view($S)) {
        if (fk_support($S)) {
            echo "<h3 id='foreign-keys'>".lang(99)."</h3>\n";
            $ed = foreign_keys($a);
            if ($ed) {
                echo "<table>\n",'<thead><tr><th>'.lang(133).'<td>'.lang(134).'<td>'.lang(102).'<td>'.lang(101)."<td></thead>\n";
                foreach ($ed as $C => $r) {
                    echo "<tr title='".h($C)."'>",'<th><i>'.implode('</i>, <i>', array_map('Adminer\h', $r['source'])).'</i>';
                    $A = ($r['db'] != '' ? preg_replace('~db=[^&]*~', 'db='.urlencode($r['db']), ME) : ($r['ns'] != '' ? preg_replace('~ns=[^&]*~', 'ns='.urlencode($r['ns']), ME) : ME));
                    echo "<td><a href='".h($A.'table='.urlencode($r['table']))."'>".($r['db'] != '' && $r['db'] != DB ? '<b>'.h($r['db']).'</b>.' : '').($r['ns'] != '' && $r['ns'] != $_GET['ns'] ? '<b>'.h($r['ns']).'</b>.' : '').h($r['table']).'</a>','(<i>'.implode('</i>, <i>', array_map('Adminer\h', $r['target'])).'</i>)','<td>'.h($r['on_delete']),'<td>'.h($r['on_update']),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($C)).'">'.lang(135).'</a>',"\n";
                }echo "</table>\n";
            }echo '<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.lang(136)."</a>\n";
        }if (support('check')) {
            echo "<h3 id='checks'>".lang(137)."</h3>\n";
            $Wa = $m->checkConstraints($a);
            if ($Wa) {
                echo "<table>\n";
                foreach ($Wa as $z => $X) {
                    echo "<tr title='".h($z)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($z))."'>".lang(135).'</a>',"\n";
                }echo "</table>\n";
            }echo '<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.lang(138)."</a>\n";
        }
    }if (support(is_view($S) ? 'view_trigger' : 'trigger')) {
        echo "<h3 id='triggers'>".lang(139)."</h3>\n";
        $yi = triggers($a);
        if ($yi) {
            echo "<table>\n";
            foreach ($yi as $z => $X) {
                echo "<tr valign='top'><td>".h($X[0]).'<td>'.h($X[1]).'<th>'.h($z)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($z))."'>".lang(135)."</a>\n";
            }echo "</table>\n";
        }echo '<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.lang(140)."</a>\n";
    }
} elseif (isset($_GET['schema'])) {
    page_header(lang(69), '', [], h(DB.($_GET['ns'] ? ".$_GET[ns]" : '')));
    $Qh = [];
    $Rh = [];
    $ea = ($_GET['schema'] ?: $_COOKIE['adminer_schema-'.str_replace('.', '_', DB)]);
    preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~', $ea, $De, PREG_SET_ORDER);
    foreach ($De as $u => $B) {
        $Qh[$B[1]] = [$B[2], $B[3]];
        $Rh[] = "\n\t'".js_escape($B[1])."': [ $B[2], $B[3] ]";
    }$ni = 0;
    $Ia = -1;
    $Vg = [];
    $Dg = [];
    $re = [];
    foreach (table_status('', true) as $R => $S) {
        if (is_view($S)) {
            continue;
        }$fg = 0;
        $Vg[$R]['fields'] = [];
        foreach (fields($R) as $C => $o) {
            $fg += 1.25;
            $o['pos'] = $fg;
            $Vg[$R]['fields'][$C] = $o;
        }$Vg[$R]['pos'] = ($Qh[$R] ?: [$ni, 0]);
        foreach ($b->foreignKeys($R) as $X) {
            if (! $X['db']) {
                $pe = $Ia;
                if ($Qh[$R][1] || $Qh[$X['table']][1]) {
                    $pe = min(floatval($Qh[$R][1]), floatval($Qh[$X['table']][1])) - 1;
                } else {
                    $Ia -= .1;
                }while ($re[(string) $pe]) {
                    $pe -= .0001;
                }$Vg[$R]['references'][$X['table']][(string) $pe] = [$X['source'], $X['target']];
                $Dg[$X['table']][$R][(string) $pe] = $X['target'];
                $re[(string) $pe] = true;
            }
        }$ni = max($ni, $Vg[$R]['pos'][0] + 2.5 + $fg);
    }echo '<div id="schema" style="height: ',$ni,'em;">
<script',nonce(),'>
qs(\'#schema\').onselectstart = function () { return false; };
var tablePos = {',implode(',', $Rh)."\n",'};
var em = qs(\'#schema\').offsetHeight / ',$ni,';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'',js_escape(DB),'\');
</script>
';
    foreach ($Vg as $C => $R) {
        echo "<div class='table' style='top: ".$R['pos'][0].'em; left: '.$R['pos'][1]."em;'>",'<a href="'.h(ME).'table='.urlencode($C).'"><b>'.h($C).'</b></a>',script("qsl('div').onmousedown = schemaMousedown;");
        foreach ($R['fields'] as $o) {
            $X = '<span'.type_class($o['type']).' title="'.h($o['full_type'].($o['null'] ? ' NULL' : '')).'">'.h($o['field']).'</span>';
            echo '<br>'.($o['primary'] ? "<i>$X</i>" : $X);
        }foreach ((array) $R['references'] as $Xh => $Eg) {
            foreach ($Eg as $pe => $Ag) {
                $qe = $pe - $Qh[$C][1];
                $u = 0;
                foreach ($Ag[0] as $uh) {
                    echo "\n<div class='references' title='".h($Xh)."' id='refs$pe-".($u++)."' style='left: $qe".'em; top: '.$R['fields'][$uh]['pos']."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: ".(-$qe)."em;'></div></div>";
                }
            }
        }foreach ((array) $Dg[$C] as $Xh => $Eg) {
            foreach ($Eg as $pe => $e) {
                $qe = $pe - $Qh[$C][1];
                $u = 0;
                foreach ($e as $Wh) {
                    echo "\n<div class='references' title='".h($Xh)."' id='refd$pe-".($u++)."'"." style='left: $qe".'em; top: '.$R['fields'][$Wh]['pos'].'em; height: 1.25em; background: url('.h(preg_replace('~\\?.*~', '', ME).'?file=arrow.gif) no-repeat right center;&version=5.0.6')."'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$qe)."em;'></div>".'</div>';
                }
            }
        }echo "\n</div>\n";
    }foreach ($Vg as $C => $R) {
        foreach ((array) $R['references'] as $Xh => $Eg) {
            foreach ($Eg as $pe => $Ag) {
                $Re = $ni;
                $He = -10;
                foreach ($Ag[0] as $z => $uh) {
                    $gg = $R['pos'][0] + $R['fields'][$uh]['pos'];
                    $hg = $Vg[$Xh]['pos'][0] + $Vg[$Xh]['fields'][$Ag[1][$z]]['pos'];
                    $Re = min($Re, $gg, $hg);
                    $He = max($He, $gg, $hg);
                }echo "<div class='references' id='refl$pe' style='left: $pe"."em; top: $Re"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($He - $Re)."em;'></div></div>\n";
            }
        }
    }echo '</div>
<p class="links"><a href="',h(ME.'schema='.urlencode($ea)),'" id="schema-link">',lang(141),'</a>
';
} elseif (isset($_GET['dump'])) {
    $a = $_GET['dump'];
    if ($_POST && ! $n) {
        save_settings(array_intersect_key($_POST, array_flip(['output', 'format', 'db_style', 'types', 'routines', 'events', 'table_style', 'auto_increment', 'triggers', 'data_style'])), 'adminer_export');
        $T = array_flip((array) $_POST['tables']) + array_flip((array) $_POST['data']);
        $Ic = dump_headers((count($T) == 1 ? key($T) : DB), (DB == '' || count($T) > 1));
        $Zd = preg_match('~sql~', $_POST['format']);
        if ($Zd) {
            echo "-- Adminer $ia ".$bc[DRIVER].' '.str_replace("\n", ' ', $g->server_info)." dump\n\n";
            if (JUSH == 'sql') {
                echo "SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST['data_style'] ? "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
" : '').'
';
                $g->query("SET time_zone = '+00:00'");
                $g->query("SET sql_mode = ''");
            }
        }$Gh = $_POST['db_style'];
        $j = [DB];
        if (DB == '') {
            $j = $_POST['databases'];
            if (is_string($j)) {
                $j = explode("\n", rtrim(str_replace("\r", '', $j), "\n"));
            }
        }foreach ((array) $j as $k) {
            $b->dumpDatabase($k);
            if ($g->select_db($k)) {
                if ($Zd && preg_match('~CREATE~', $Gh) && ($i = get_val('SHOW CREATE DATABASE '.idf_escape($k), 1))) {
                    set_utf8mb4($i);
                    if ($Gh == 'DROP+CREATE') {
                        echo 'DROP DATABASE IF EXISTS '.idf_escape($k).";\n";
                    }echo "$i;\n";
                }if ($Zd) {
                    if ($Gh) {
                        echo use_sql($k).";\n\n";
                    }$Lf = '';
                    if ($_POST['types']) {
                        foreach (types() as $v => $U) {
                            $xc = type_values($v);
                            if ($xc) {
                                $Lf .= ($Gh != 'DROP+CREATE' ? 'DROP TYPE IF EXISTS '.idf_escape($U).";;\n" : '').'CREATE TYPE '.idf_escape($U)." AS ENUM ($xc);\n\n";
                            } else {
                                $Lf .= "-- Could not export type $U\n\n";
                            }
                        }
                    }if ($_POST['routines']) {
                        foreach (routines() as $K) {
                            $C = $K['ROUTINE_NAME'];
                            $Pg = $K['ROUTINE_TYPE'];
                            $i = create_routine($Pg, ['name' => $C] + routine($K['SPECIFIC_NAME'], $Pg));
                            set_utf8mb4($i);
                            $Lf .= ($Gh != 'DROP+CREATE' ? "DROP $Pg IF EXISTS ".idf_escape($C).";;\n" : '')."$i;\n\n";
                        }
                    }if ($_POST['events']) {
                        foreach (get_rows('SHOW EVENTS', null, '-- ') as $K) {
                            $i = remove_definer(get_val('SHOW CREATE EVENT '.idf_escape($K['Name']), 3));
                            set_utf8mb4($i);
                            $Lf .= ($Gh != 'DROP+CREATE' ? 'DROP EVENT IF EXISTS '.idf_escape($K['Name']).";;\n" : '')."$i;;\n\n";
                        }
                    }echo $Lf && JUSH == 'sql' ? "DELIMITER ;;\n\n$Lf"."DELIMITER ;\n\n" : $Lf;
                }if ($_POST['table_style'] || $_POST['data_style']) {
                    $Zi = [];
                    foreach (table_status('', true) as $C => $S) {
                        $R = (DB == '' || in_array($C, (array) $_POST['tables']));
                        $Ib = (DB == '' || in_array($C, (array) $_POST['data']));
                        if ($R || $Ib) {
                            if ($Ic == 'tar') {
                                $ki = new TmpFile;
                                ob_start([$ki, 'write'], 1e5);
                            }$b->dumpTable($C, ($R ? $_POST['table_style'] : ''), (is_view($S) ? 2 : 0));
                            if (is_view($S)) {
                                $Zi[] = $C;
                            } elseif ($Ib) {
                                $p = fields($C);
                                $b->dumpData($C, $_POST['data_style'], 'SELECT *'.convert_fields($p, $p).' FROM '.table($C));
                            }if ($Zd && $_POST['triggers'] && $R && ($yi = trigger_sql($C))) {
                                echo "\nDELIMITER ;;\n$yi\nDELIMITER ;\n";
                            }if ($Ic == 'tar') {
                                ob_end_flush();
                                tar_file((DB != '' ? '' : "$k/")."$C.csv", $ki);
                            } elseif ($Zd) {
                                echo "\n";
                            }
                        }
                    }if (function_exists('Adminer\foreign_keys_sql')) {
                        foreach (table_status('', true) as $C => $S) {
                            $R = (DB == '' || in_array($C, (array) $_POST['tables']));
                            if ($R && ! is_view($S)) {
                                echo foreign_keys_sql($C);
                            }
                        }
                    }foreach ($Zi as $Yi) {
                        $b->dumpTable($Yi, $_POST['table_style'], 1);
                    }if ($Ic == 'tar') {
                        echo pack('x512');
                    }
                }
            }
        }$b->dumpFooter();
        exit;
    }page_header(lang(72), $n, ($_GET['export'] != '' ? ['table' => $_GET['export']] : []), h(DB));
    echo '
<form action="" method="post">
<table class="layout">
';
    $Mb = ['', 'USE', 'DROP+CREATE', 'CREATE'];
    $Sh = ['', 'DROP+CREATE', 'CREATE'];
    $Jb = ['', 'TRUNCATE+INSERT', 'INSERT'];
    if (JUSH == 'sql') {
        $Jb[] = 'INSERT+UPDATE';
    }$K = get_settings('adminer_export');
    if (! $K) {
        $K = ['output' => 'text', 'format' => 'sql', 'db_style' => (DB != '' ? '' : 'CREATE'), 'table_style' => 'DROP+CREATE', 'data_style' => 'INSERT'];
    }if (! isset($K['events'])) {
        $K['routines'] = $K['events'] = ($_GET['dump'] == '');
        $K['triggers'] = $K['table_style'];
    }echo '<tr><th>'.lang(142).'<td>'.html_radios('output', $b->dumpOutput(), $K['output'])."\n",'<tr><th>'.lang(143).'<td>'.html_radios('format', $b->dumpFormat(), $K['format'])."\n",(JUSH == 'sqlite' ? '' : '<tr><th>'.lang(36).'<td>'.html_select('db_style', $Mb, $K['db_style']).(support('type') ? checkbox('types', 1, $K['types'], lang(31)) : '').(support('routine') ? checkbox('routines', 1, $K['routines'], lang(144)) : '').(support('event') ? checkbox('events', 1, $K['events'], lang(145)) : '')),'<tr><th>'.lang(122).'<td>'.html_select('table_style', $Sh, $K['table_style']).checkbox('auto_increment', 1, $K['auto_increment'], lang(50)).(support('trigger') ? checkbox('triggers', 1, $K['triggers'], lang(139)) : ''),'<tr><th>'.lang(146).'<td>'.html_select('data_style', $Jb, $K['data_style']),'</table>
<p><input type="submit" value="',lang(72),'">
<input type="hidden" name="token" value="',$mi,'">

<table>
',script("qsl('table').onclick = dumpClick;");
    $lg = [];
    if (DB != '') {
        $Ya = ($a != '' ? '' : ' checked');
        echo '<thead><tr>',"<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Ya>".lang(122).'</label>'.script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);", ''),"<th style='text-align: right;'><label class='block'>".lang(146)."<input type='checkbox' id='check-data'$Ya></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);", ''),"</thead>\n";
        $Zi = '';
        $Th = tables_list();
        foreach ($Th as $C => $U) {
            $kg = preg_replace('~_.*~', '', $C);
            $Ya = ($a == '' || $a == (substr($a, -1) == '%' ? "$kg%" : $C));
            $ng = '<tr><td>'.checkbox('tables[]', $C, $Ya, $C, '', 'block');
            if ($U !== null && ! preg_match('~table~i', $U)) {
                $Zi .= "$ng\n";
            } else {
                echo "$ng<td align='right'><label class='block'><span id='Rows-".h($C)."'></span>".checkbox('data[]', $C, $Ya)."</label>\n";
            }$lg[$kg]++;
        }echo $Zi;
        if ($Th) {
            echo script("ajaxSetHtml('".js_escape(ME)."script=db');");
        }
    } else {
        echo "<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-databases'".($a == '' ? ' checked' : '').'>'.lang(36).'</label>',script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);", ''),"</thead>\n";
        $j = $b->databases();
        if ($j) {
            foreach ($j as $k) {
                if (! information_schema($k)) {
                    $kg = preg_replace('~_.*~', '', $k);
                    echo '<tr><td>'.checkbox('databases[]', $k, $a == '' || $a == "$kg%", $k, '', 'block')."\n";
                    $lg[$kg]++;
                }
            }
        } else {
            echo "<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";
        }
    }echo '</table>
</form>
';
    $Vc = true;
    foreach ($lg as $z => $X) {
        if ($z != '' && $X > 1) {
            echo ($Vc ? '<p>' : ' ')."<a href='".h(ME).'dump='.urlencode("$z%")."'>".h($z).'</a>';
            $Vc = false;
        }
    }
} elseif (isset($_GET['privileges'])) {
    page_header(lang(70));
    echo '<p class="links"><a href="'.h(ME).'user=">'.lang(147).'</a>';
    $I = $g->query('SELECT User, Host FROM mysql.'.(DB == '' ? 'user' : 'db WHERE '.q(DB).' LIKE Db').' ORDER BY Host, User');
    $nd = $I;
    if (! $I) {
        $I = $g->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");
    }echo "<form action=''><p>\n";
    hidden_fields_get();
    echo "<input type='hidden' name='db' value='".h(DB)."'>\n",($nd ? '' : "<input type='hidden' name='grant' value=''>\n"),"<table class='odds'>\n",'<thead><tr><th>'.lang(34).'<th>'.lang(33)."<th></thead>\n";
    while ($K = $I->fetch_assoc()) {
        echo '<tr><td>'.h($K['User']).'<td>'.h($K['Host']).'<td><a href="'.h(ME.'user='.urlencode($K['User']).'&host='.urlencode($K['Host'])).'">'.lang(10)."</a>\n";
    }if (! $nd || DB != '') {
        echo "<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".lang(10)."'>\n";
    }echo "</table>\n","</form>\n";
} elseif (isset($_GET['sql'])) {
    if (! $n && $_POST['export']) {
        save_settings(['output' => $_POST['output'], 'format' => $_POST['format']], 'adminer_import');
        dump_headers('sql');
        $b->dumpTable('', '');
        $b->dumpData('', 'table', $_POST['query']);
        $b->dumpFooter();
        exit;
    }restart_session();
    $Bd = &get_session('queries');
    $Ad = &$Bd[DB];
    if (! $n && $_POST['clear']) {
        $Ad = [];
        redirect(remove_from_uri('history'));
    }page_header((isset($_GET['import']) ? lang(71) : lang(63)), $n);
    if (! $n && $_POST) {
        $s = false;
        if (! isset($_GET['import'])) {
            $H = $_POST['query'];
        } elseif ($_POST['webfile']) {
            $yh = $b->importServerPath();
            $s = @fopen((file_exists($yh) ? $yh : "compress.zlib://$yh.gz"), 'rb');
            $H = ($s ? fread($s, 1e6) : false);
        } else {
            $H = get_file('sql_file', true, ';');
        }if (is_string($H)) {
            if (function_exists('memory_get_usage') && ($Le = ini_bytes('memory_limit')) != '-1') {
                @ini_set('memory_limit', max($Le, 2 * strlen($H) + memory_get_usage() + 8e6));
            }if ($H != '' && strlen($H) < 1e6) {
                $ug = $H.(preg_match("~;[ \t\r\n]*\$~", $H) ? '' : ';');
                if (! $Ad || reset(end($Ad)) != $ug) {
                    restart_session();
                    $Ad[] = [$ug, time()];
                    set_session('queries', $Bd);
                    stop_session();
                }
            }$vh = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
            $Sb = ';';
            $D = 0;
            $rc = true;
            $h = connect($b->credentials());
            if (is_object($h) && DB != '') {
                $h->select_db(DB);
                if ($_GET['ns'] != '') {
                    set_schema($_GET['ns'], $h);
                }
            }$mb = 0;
            $zc = [];
            $Sf = '[\'"'.(JUSH == 'sql' ? '`#' : (JUSH == 'sqlite' ? '`[' : (JUSH == 'mssql' ? '[' : ''))).']|/\*|-- |$'.(JUSH == 'pgsql' ? '|\$[^$]*\$' : '');
            $oi = microtime(true);
            $pa = get_settings('adminer_import');
            $ic = $b->dumpFormat();
            unset($ic['sql']);
            while ($H != '') {
                if (! $D && preg_match("~^$vh*+DELIMITER\\s+(\\S+)~i", $H, $B)) {
                    $Sb = $B[1];
                    $H = substr($H, strlen($B[0]));
                } else {
                    preg_match('('.preg_quote($Sb)."\\s*|$Sf)", $H, $B, PREG_OFFSET_CAPTURE, $D);
                    [$gd, $fg] = $B[0];
                    if (! $gd && $s && ! feof($s)) {
                        $H .= fread($s, 1e5);
                    } else {
                        if (! $gd && rtrim($H) == '') {
                            break;
                        }$D = $fg + strlen($gd);
                        if ($gd && rtrim($gd) != $Sb) {
                            $Ra = $m->hasCStyleEscapes() || (JUSH == 'pgsql' && ($fg > 0 && strtolower($H[$fg - 1]) == 'e'));
                            $ag = ($gd == '/*' ? '\*/' : ($gd == '[' ? ']' : (preg_match('~^-- |^#~', $gd) ? "\n" : preg_quote($gd).($Ra ? '|\\\\.' : ''))));
                            while (preg_match("($ag|\$)s", $H, $B, PREG_OFFSET_CAPTURE, $D)) {
                                $Tg = $B[0][0];
                                if (! $Tg && $s && ! feof($s)) {
                                    $H .= fread($s, 1e5);
                                } else {
                                    $D = $B[0][1] + strlen($Tg);
                                    if (! $Tg || $Tg[0] != '\\') {
                                        break;
                                    }
                                }
                            }
                        } else {
                            $rc = false;
                            $ug = substr($H, 0, $fg);
                            $mb++;
                            $ng = "<pre id='sql-$mb'><code class='jush-".JUSH."'>".$b->sqlCommandQuery($ug)."</code></pre>\n";
                            if (JUSH == 'sqlite' && preg_match("~^$vh*+ATTACH\\b~i", $ug, $B)) {
                                echo $ng,"<p class='error'>".lang(148)."\n";
                                $zc[] = " <a href='#sql-$mb'>$mb</a>";
                                if ($_POST['error_stops']) {
                                    break;
                                }
                            } else {
                                if (! $_POST['only_errors']) {
                                    echo $ng;
                                    ob_flush();
                                    flush();
                                }$Ch = microtime(true);
                                if ($g->multi_query($ug) && is_object($h) && preg_match("~^$vh*+USE\\b~i", $ug)) {
                                    $h->query($ug);
                                }do {
                                    $I = $g->store_result();
                                    if ($g->error) {
                                        echo ($_POST['only_errors'] ? $ng : ''),"<p class='error'>".lang(149).($g->errno ? " ($g->errno)" : '').': '.error()."\n";
                                        $zc[] = " <a href='#sql-$mb'>$mb</a>";
                                        if ($_POST['error_stops']) {
                                            break 2;
                                        }
                                    } else {
                                        $di = " <span class='time'>(".format_time($Ch).')</span>'.(strlen($ug) < 1000 ? " <a href='".h(ME).'sql='.urlencode(trim($ug))."'>".lang(10).'</a>' : '');
                                        $ra = $g->affected_rows;
                                        $cj = ($_POST['only_errors'] ? '' : $m->warnings());
                                        $dj = "warnings-$mb";
                                        if ($cj) {
                                            $di .= ", <a href='#$dj'>".lang(45).'</a>'.script("qsl('a').onclick = partial(toggle, '$dj');", '');
                                        }$Gc = null;
                                        $Hc = "explain-$mb";
                                        if (is_object($I)) {
                                            $_ = $_POST['limit'];
                                            $Df = select($I, $h, [], $_);
                                            if (! $_POST['only_errors']) {
                                                echo "<form action='' method='post'>\n";
                                                $ff = $I->num_rows;
                                                echo '<p>'.($ff ? ($_ && $ff > $_ ? lang(150, $_) : '').lang(151, $ff) : ''),$di;
                                                if ($h && preg_match("~^($vh|\\()*+SELECT\\b~i", $ug) && ($Gc = explain($h, $ug))) {
                                                    echo ", <a href='#$Hc'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$Hc');", '');
                                                }$v = "export-$mb";
                                                echo ", <a href='#$v'>".lang(72).'</a>'.script("qsl('a').onclick = partial(toggle, '$v');", '')."<span id='$v' class='hidden'>: ".html_select('output', $b->dumpOutput(), $pa['output']).' '.html_select('format', $ic, $pa['format'])."<input type='hidden' name='query' value='".h($ug)."'>"." <input type='submit' name='export' value='".lang(72)."'><input type='hidden' name='token' value='$mi'></span>\n"."</form>\n";
                                            }
                                        } else {
                                            if (preg_match("~^$vh*+(CREATE|DROP|ALTER)$vh++(DATABASE|SCHEMA)\\b~i", $ug)) {
                                                restart_session();
                                                set_session('dbs', null);
                                                stop_session();
                                            }if (! $_POST['only_errors']) {
                                                echo "<p class='message' title='".h($g->info)."'>".lang(152, $ra)."$di\n";
                                            }
                                        }echo $cj ? "<div id='$dj' class='hidden'>\n$cj</div>\n" : '';
                                        if ($Gc) {
                                            echo "<div id='$Hc' class='hidden explain'>\n";
                                            select($Gc, $h, $Df);
                                            echo "</div>\n";
                                        }
                                    }$Ch = microtime(true);
                                } while ($g->next_result());
                            }$H = substr($H, $D);
                            $D = 0;
                        }
                    }
                }
            }if ($rc) {
                echo "<p class='message'>".lang(153)."\n";
            } elseif ($_POST['only_errors']) {
                echo "<p class='message'>".lang(154, $mb - count($zc))," <span class='time'>(".format_time($oi).")</span>\n";
            } elseif ($zc && $mb > 1) {
                echo "<p class='error'>".lang(149).': '.implode('', $zc)."\n";
            }
        } else {
            echo "<p class='error'>".upload_error($H)."\n";
        }
    }echo '
<form action="" method="post" enctype="multipart/form-data" id="form">
';
    $Ec = "<input type='submit' value='".lang(155)."' title='Ctrl+Enter'>";
    if (! isset($_GET['import'])) {
        $ug = $_GET['sql'];
        if ($_POST) {
            $ug = $_POST['query'];
        } elseif ($_GET['history'] == 'all') {
            $ug = $Ad;
        } elseif ($_GET['history'] != '') {
            $ug = $Ad[$_GET['history']][0];
        }echo '<p>';
        textarea('query', $ug, 20);
        echo script(($_POST ? '' : "qs('textarea').focus();\n")."qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '".js_escape(remove_from_uri('sql|limit|error_stops|only_errors|history'))."');"),"<p>$Ec\n",lang(156).": <input type='number' name='limit' class='size' value='".h($_POST ? $_POST['limit'] : $_GET['limit'])."'>\n";
    } else {
        echo '<fieldset><legend>'.lang(157).'</legend><div>';
        $td = (extension_loaded('zlib') ? '[.gz]' : '');
        echo (ini_bool('file_uploads') ? "SQL$td (&lt; ".ini_get('upload_max_filesize')."B): <input type='file' name='sql_file[]' multiple>\n$Ec" : lang(158)),"</div></fieldset>\n";
        $Id = $b->importServerPath();
        if ($Id) {
            echo '<fieldset><legend>'.lang(159).'</legend><div>',lang(160, '<code>'.h($Id)."$td</code>"),' <input type="submit" name="webfile" value="'.lang(161).'">',"</div></fieldset>\n";
        }echo '<p>';
    }echo checkbox('error_stops', 1, ($_POST ? $_POST['error_stops'] : isset($_GET['import']) || $_GET['error_stops']), lang(162))."\n",checkbox('only_errors', 1, ($_POST ? $_POST['only_errors'] : isset($_GET['import']) || $_GET['only_errors']), lang(163))."\n","<input type='hidden' name='token' value='$mi'>\n";
    if (! isset($_GET['import']) && $Ad) {
        print_fieldset('history', lang(164), $_GET['history'] != '');
        for ($X = end($Ad); $X; $X = prev($Ad)) {
            $z = key($Ad);
            [$ug, $di, $mc] = $X;
            echo '<a href="'.h(ME."sql=&history=$z").'">'.lang(10).'</a>'." <span class='time' title='".@date('Y-m-d', $di)."'>".@date('H:i:s', $di).'</span>'." <code class='jush-".JUSH."'>".shorten_utf8(ltrim(str_replace("\n", ' ', str_replace("\r", '', preg_replace('~^(#|-- ).*~m', '', $ug)))), 80, '</code>').($mc ? " <span class='time'>($mc)</span>" : '')."<br>\n";
        }echo "<input type='submit' name='clear' value='".lang(165)."'>\n","<a href='".h(ME.'sql=&history=all')."'>".lang(166)."</a>\n","</div></fieldset>\n";
    }echo '</form>
';
} elseif (isset($_GET['edit'])) {
    $a = $_GET['edit'];
    $p = fields($a);
    $Z = (isset($_GET['select']) ? ($_POST['check'] && count($_POST['check']) == 1 ? where_check($_POST['check'][0], $p) : '') : where($_GET, $p));
    $Ji = (isset($_GET['select']) ? $_POST['edit'] : $Z);
    foreach ($p as $C => $o) {
        if (! isset($o['privileges'][$Ji ? 'update' : 'insert']) || $b->fieldName($o) == '' || $o['generated']) {
            unset($p[$C]);
        }
    }if ($_POST && ! $n && ! isset($_GET['select'])) {
        $xe = $_POST['referer'];
        if ($_POST['insert']) {
            $xe = ($Ji ? null : $_SERVER['REQUEST_URI']);
        } elseif (! preg_match('~^.+&select=.+$~', $xe)) {
            $xe = ME.'select='.urlencode($a);
        }$y = indexes($a);
        $Ei = unique_array($_GET['where'], $y);
        $xg = "\nWHERE $Z";
        if (isset($_POST['delete'])) {
            queries_redirect($xe, lang(167), $m->delete($a, $xg, ! $Ei));
        } else {
            $O = [];
            foreach ($p as $C => $o) {
                $X = process_input($o);
                if ($X !== false && $X !== null) {
                    $O[idf_escape($C)] = $X;
                }
            }if ($Ji) {
                if (! $O) {
                    redirect($xe);
                }queries_redirect($xe, lang(168), $m->update($a, $O, $xg, ! $Ei));
                if (is_ajax()) {
                    page_headers();
                    page_messages($n);
                    exit;
                }
            } else {
                $I = $m->insert($a, $O);
                $oe = ($I ? last_id() : 0);
                queries_redirect($xe, lang(169, ($oe ? " $oe" : '')), $I);
            }
        }
    }$K = null;
    if ($_POST['save']) {
        $K = (array) $_POST['fields'];
    } elseif ($Z) {
        $M = [];
        foreach ($p as $C => $o) {
            if (isset($o['privileges']['select'])) {
                $ya = ($_POST['clone'] && $o['auto_increment'] ? "''" : convert_field($o));
                $M[] = ($ya ? "$ya AS " : '').idf_escape($C);
            }
        }$K = [];
        if (! support('table')) {
            $M = ['*'];
        }if ($M) {
            $I = $m->select($a, $M, [$Z], $M, [], (isset($_GET['select']) ? 2 : 1));
            if (! $I) {
                $n = error();
            } else {
                $K = $I->fetch_assoc();
                if (! $K) {
                    $K = false;
                }
            }if (isset($_GET['select']) && (! $K || $I->fetch_assoc())) {
                $K = null;
            }
        }
    }if (! support('table') && ! $p) {
        if (! $Z) {
            $I = $m->select($a, ['*'], $Z, ['*']);
            $K = ($I ? $I->fetch_assoc() : false);
            if (! $K) {
                $K = [$m->primary => ''];
            }
        }if ($K) {
            foreach ($K as $z => $X) {
                if (! $Z) {
                    $K[$z] = null;
                }$p[$z] = ['field' => $z, 'null' => ($z != $m->primary), 'auto_increment' => ($z == $m->primary)];
            }
        }
    }edit_form($a, $p, $K, $Ji);
} elseif (isset($_GET['create'])) {
    $a = $_GET['create'];
    $Uf = [];
    foreach (['HASH', 'LINEAR HASH', 'KEY', 'LINEAR KEY', 'RANGE', 'LIST'] as $z) {
        $Uf[$z] = $z;
    }$Cg = referencable_primary($a);
    $ed = [];
    foreach ($Cg as $Oh => $o) {
        $ed[str_replace('`', '``', $Oh).'`'.str_replace('`', '``', $o['field'])] = $Oh;
    }$Gf = [];
    $S = [];
    if ($a != '') {
        $Gf = fields($a);
        $S = table_status($a);
        if (! $S) {
            $n = lang(9);
        }
    }$K = $_POST;
    $K['fields'] = (array) $K['fields'];
    if ($K['auto_increment_col']) {
        $K['fields'][$K['auto_increment_col']]['auto_increment'] = true;
    }if ($_POST) {
        save_settings(['comments' => $_POST['comments'], 'defaults' => $_POST['defaults']]);
    }if ($_POST && ! process_fields($K['fields']) && ! $n) {
        if ($_POST['drop']) {
            queries_redirect(substr(ME, 0, -1), lang(170), drop_tables([$a]));
        } else {
            $p = [];
            $va = [];
            $Ni = false;
            $cd = [];
            $Ff = reset($Gf);
            $ta = ' FIRST';
            foreach ($K['fields'] as $z => $o) {
                $r = $ed[$o['type']];
                $zi = ($r !== null ? $Cg[$r] : $o);
                if ($o['field'] != '') {
                    if (! $o['generated']) {
                        $o['default'] = null;
                    }$sg = process_field($o, $zi);
                    $va[] = [$o['orig'], $sg, $ta];
                    if (! $Ff || $sg !== process_field($Ff, $Ff)) {
                        $p[] = [$o['orig'], $sg, $ta];
                        if ($o['orig'] != '' || $ta) {
                            $Ni = true;
                        }
                    }if ($r !== null) {
                        $cd[idf_escape($o['field'])] = ($a != '' && JUSH != 'sqlite' ? 'ADD' : ' ').format_foreign_key(['table' => $ed[$o['type']], 'source' => [$o['field']], 'target' => [$zi['field']], 'on_delete' => $o['on_delete']]);
                    }$ta = ' AFTER '.idf_escape($o['field']);
                } elseif ($o['orig'] != '') {
                    $Ni = true;
                    $p[] = [$o['orig']];
                }if ($o['orig'] != '') {
                    $Ff = next($Gf);
                    if (! $Ff) {
                        $ta = '';
                    }
                }
            }$Wf = '';
            if (support('partitioning')) {
                if (isset($Uf[$K['partition_by']])) {
                    $Rf = [];
                    foreach ($K as $z => $X) {
                        if (preg_match('~^partition~', $z)) {
                            $Rf[$z] = $X;
                        }
                    }foreach ($Rf['partition_names'] as $z => $C) {
                        if ($C == '') {
                            unset($Rf['partition_names'][$z]);
                            unset($Rf['partition_values'][$z]);
                        }
                    }if ($Rf != get_partitions_info($a)) {
                        $Xf = [];
                        if ($Rf['partition_by'] == 'RANGE' || $Rf['partition_by'] == 'LIST') {
                            foreach ($Rf['partition_names'] as $z => $C) {
                                $Y = $Rf['partition_values'][$z];
                                $Xf[] = "\n  PARTITION ".idf_escape($C).' VALUES '.($Rf['partition_by'] == 'RANGE' ? 'LESS THAN' : 'IN').($Y != '' ? " ($Y)" : ' MAXVALUE');
                            }
                        }$Wf .= "\nPARTITION BY $Rf[partition_by]($Rf[partition])";
                        if ($Xf) {
                            $Wf .= ' ('.implode(',', $Xf)."\n)";
                        } elseif ($Rf['partitions']) {
                            $Wf .= ' PARTITIONS '.(+$Rf['partitions']);
                        }
                    }
                } elseif (preg_match('~partitioned~', $S['Create_options'])) {
                    $Wf .= "\nREMOVE PARTITIONING";
                }
            }$Me = lang(171);
            if ($a == '') {
                cookie('adminer_engine', $K['Engine']);
                $Me = lang(172);
            }$C = trim($K['name']);
            queries_redirect(ME.(support('table') ? 'table=' : 'select=').urlencode($C), $Me, alter_table($a, $C, (JUSH == 'sqlite' && ($Ni || $cd) ? $va : $p), $cd, ($K['Comment'] != $S['Comment'] ? $K['Comment'] : null), ($K['Engine'] && $K['Engine'] != $S['Engine'] ? $K['Engine'] : ''), ($K['Collation'] && $K['Collation'] != $S['Collation'] ? $K['Collation'] : ''), ($K['Auto_increment'] != '' ? number($K['Auto_increment']) : ''), $Wf));
        }
    }page_header(($a != '' ? lang(43) : lang(73)), $n, ['table' => $a], h($a));
    if (! $_POST) {
        $Ai = $m->types();
        $K = ['Engine' => $_COOKIE['adminer_engine'], 'fields' => [['field' => '', 'type' => (isset($Ai['int']) ? 'int' : (isset($Ai['integer']) ? 'integer' : '')), 'on_update' => '']], 'partition_names' => ['']];
        if ($a != '') {
            $K = $S;
            $K['name'] = $a;
            $K['fields'] = [];
            if (! $_GET['auto_increment']) {
                $K['Auto_increment'] = '';
            }foreach ($Gf as $o) {
                $o['generated'] = $o['generated'] ?: (isset($o['default']) ? 'DEFAULT' : '');
                $K['fields'][] = $o;
            }if (support('partitioning')) {
                $K += get_partitions_info($a);
                $K['partition_names'][] = '';
                $K['partition_values'][] = '';
            }
        }
    }$ib = collations();
    $tc = engines();
    foreach ($tc as $sc) {
        if (! strcasecmp($sc, $K['Engine'])) {
            $K['Engine'] = $sc;
            break;
        }
    }echo '
<form action="" method="post" id="form">
<p>
';
    if (support('columns') || $a == '') {
        echo lang(173)."<input name='name'".($a == '' && ! $_POST ? ' autofocus' : '')." data-maxlength='64' value='".h($K['name'])."' autocapitalize='off'>\n",($tc ? html_select('Engine', ['' => '('.lang(174).')'] + $tc, $K['Engine']).on_help('getTarget(event).value', 1).script("qsl('select').onchange = helpClose;")."\n" : '');
        if ($ib) {
            echo "<datalist id='collations'>".optionlist($ib).'</datalist>',(preg_match('~sqlite|mssql~', JUSH) ? '' : "<input list='collations' name='Collation' value='".h($K['Collation'])."' placeholder='(".lang(100).")'>");
        }echo "<input type='submit' value='".lang(14)."'>\n";
    }if (support('columns')) {
        echo "<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";
        edit_fields($K['fields'], $ib, 'TABLE', $ed);
        echo "</table>\n",script('editFields();'),"</div>\n<p>\n",lang(50).": <input type='number' name='Auto_increment' class='size' value='".h($K['Auto_increment'])."'>\n",checkbox('defaults', 1, ($_POST ? $_POST['defaults'] : get_setting('defaults')), lang(175), 'columnShow(this.checked, 5)', 'jsonly');
        $pb = ($_POST ? $_POST['comments'] : get_setting('comments'));
        echo (support('comment') ? checkbox('comments', 1, $pb, lang(49), 'editingCommentsClick(this, true);', 'jsonly').' '.(preg_match('~\n~', $K['Comment']) ? "<textarea name='Comment' rows='2' cols='20'".($pb ? '' : " class='hidden'").'>'.h($K['Comment']).'</textarea>' : '<input name="Comment" value="'.h($K['Comment']).'" data-maxlength="'.(min_version(5.5) ? 2048 : 60).'"'.($pb ? '' : " class='hidden'").'>') : ''),'<p>
<input type="submit" value="',lang(14),'">
';
    }echo '
';
    if ($a != '') {
        echo '<input type="submit" name="drop" value="',lang(126),'">',confirm(lang(176, $a));
    }if (support('partitioning')) {
        $Vf = preg_match('~RANGE|LIST~', $K['partition_by']);
        print_fieldset('partition', lang(177), $K['partition_by']);
        echo '<p>'.html_select('partition_by', ['' => ''] + $Uf, $K['partition_by']).on_help("getTarget(event).value.replace(/./, 'PARTITION BY \$&')", 1).script("qsl('select').onchange = partitionByChange;"),"(<input name='partition' value='".h($K['partition'])."'>)\n",lang(178).": <input type='number' name='partitions' class='size".($Vf || ! $K['partition_by'] ? ' hidden' : '')."' value='".h($K['partitions'])."'>\n","<table id='partition-table'".($Vf ? '' : " class='hidden'").">\n",'<thead><tr><th>'.lang(179).'<th>'.lang(180)."</thead>\n";
        foreach ($K['partition_names'] as $z => $X) {
            echo '<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($z == count($K['partition_names']) - 1 ? script("qsl('input').oninput = partitionNameChange;") : ''),'<td><input name="partition_values[]" value="'.h($K['partition_values'][$z]).'">';
        }echo "</table>\n</div></fieldset>\n";
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['indexes'])) {
    $a = $_GET['indexes'];
    $Md = ['PRIMARY', 'UNIQUE', 'INDEX'];
    $S = table_status($a, true);
    if (preg_match('~MyISAM|M?aria'.(min_version(5.6, '10.0.5') ? '|InnoDB' : '').'~i', $S['Engine'])) {
        $Md[] = 'FULLTEXT';
    }if (preg_match('~MyISAM|M?aria'.(min_version(5.7, '10.2.2') ? '|InnoDB' : '').'~i', $S['Engine'])) {
        $Md[] = 'SPATIAL';
    }$y = indexes($a);
    $G = [];
    if (JUSH == 'mongo') {
        $G = $y['_id_'];
        unset($Md[0]);
        unset($y['_id_']);
    }$K = $_POST;
    if ($K) {
        save_settings(['index_options' => $K['options']]);
    }if ($_POST && ! $n && ! $_POST['add'] && ! $_POST['drop_col']) {
        $c = [];
        foreach ($K['indexes'] as $x) {
            $C = $x['name'];
            if (in_array($x['type'], $Md)) {
                $e = [];
                $ue = [];
                $Ub = [];
                $O = [];
                ksort($x['columns']);
                foreach ($x['columns'] as $z => $d) {
                    if ($d != '') {
                        $te = $x['lengths'][$z];
                        $Tb = $x['descs'][$z];
                        $O[] = idf_escape($d).($te ? '('.(+$te).')' : '').($Tb ? ' DESC' : '');
                        $e[] = $d;
                        $ue[] = ($te ?: null);
                        $Ub[] = $Tb;
                    }
                }$Fc = $y[$C];
                if ($Fc) {
                    ksort($Fc['columns']);
                    ksort($Fc['lengths']);
                    ksort($Fc['descs']);
                    if ($x['type'] == $Fc['type'] && array_values($Fc['columns']) === $e && (! $Fc['lengths'] || array_values($Fc['lengths']) === $ue) && array_values($Fc['descs']) === $Ub) {
                        unset($y[$C]);

                        continue;
                    }
                }if ($e) {
                    $c[] = [$x['type'], $C, $O];
                }
            }
        }foreach ($y as $C => $Fc) {
            $c[] = [$Fc['type'], $C, 'DROP'];
        }if (! $c) {
            redirect(ME.'table='.urlencode($a));
        }queries_redirect(ME.'table='.urlencode($a), lang(181), alter_indexes($a, $c));
    }page_header(lang(131), $n, ['table' => $a], h($a));
    $p = array_keys(fields($a));
    if ($_POST['add']) {
        foreach ($K['indexes'] as $z => $x) {
            if ($x['columns'][count($x['columns'])] != '') {
                $K['indexes'][$z]['columns'][] = '';
            }
        }$x = end($K['indexes']);
        if ($x['type'] || array_filter($x['columns'], 'strlen')) {
            $K['indexes'][] = ['columns' => [1 => '']];
        }
    }if (! $K) {
        foreach ($y as $z => $x) {
            $y[$z]['name'] = $z;
            $y[$z]['columns'][] = '';
        }$y[] = ['columns' => [1 => '']];
        $K['indexes'] = $y;
    }$ue = (JUSH == 'sql' || JUSH == 'mssql');
    $oh = ($_POST ? $_POST['options'] : get_setting('index_options'));
    echo '
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">',lang(182),'<th><input type="submit" class="wayoff">',lang(47).($ue ? "<span class='idxopts".($oh ? '' : ' hidden')."'> (".lang(183).')</span>' : '');
    if ($ue || support('descidx')) {
        echo checkbox('options', 1, $oh, lang(106), 'indexOptionsShow(this.checked)', 'jsonly')."\n";
    }echo '<th id="label-name">',lang(184),'<th><noscript>',"<input type='image' class='icon' name='add[0]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=plus.gif&version=5.0.6')."' alt='+' title='".lang(107)."'>",'</noscript>
</thead>
';
    if ($G) {
        echo '<tr><td>PRIMARY<td>';
        foreach ($G['columns'] as $z => $d) {
            echo select_input(' disabled', $p, $d),"<label><input disabled type='checkbox'>".lang(58).'</label> ';
        }echo "<td><td>\n";
    }$ce = 1;
    foreach ($K['indexes'] as $x) {
        if (! $_POST['drop_col'] || $ce != key($_POST['drop_col'])) {
            echo '<tr><td>'.html_select("indexes[$ce][type]", [-1 => ''] + $Md, $x['type'], ($ce == count($K['indexes']) ? 'indexesAddRow.call(this);' : ''), 'label-type'),'<td>';
            ksort($x['columns']);
            $u = 1;
            foreach ($x['columns'] as $z => $d) {
                echo '<span>'.select_input(" name='indexes[$ce][columns][$u]' title='".lang(47)."'", ($p ? array_combine($p, $p) : $p), $d, 'partial('.($u == count($x['columns']) ? 'indexesAddColumn' : 'indexesChangeColumn').", '".js_escape(JUSH == 'sql' ? '' : $_GET['indexes'].'_')."')"),"<span class='idxopts".($oh ? '' : ' hidden')."'>",($ue ? "<input type='number' name='indexes[$ce][lengths][$u]' class='size' value='".h($x['lengths'][$z])."' title='".lang(105)."'>" : ''),(support('descidx') ? checkbox("indexes[$ce][descs][$u]", 1, $x['descs'][$z], lang(58)) : ''),'</span> </span>';
                $u++;
            }echo "<td><input name='indexes[$ce][name]' value='".h($x['name'])."' autocapitalize='off' aria-labelledby='label-name'>\n","<td><input type='image' class='icon' name='drop_col[$ce]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=cross.gif&version=5.0.6')."' alt='x' title='".lang(110)."'>".script("qsl('input').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");
        }$ce++;
    }echo '</table>
</div>
<p>
<input type="submit" value="',lang(14),'">
<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['database'])) {
    $K = $_POST;
    if ($_POST && ! $n && ! isset($_POST['add_x'])) {
        $C = trim($K['name']);
        if ($_POST['drop']) {
            $_GET['db'] = '';
            queries_redirect(remove_from_uri('db|database'), lang(185), drop_databases([DB]));
        } elseif ($C !== DB) {
            if (DB != '') {
                $_GET['db'] = $C;
                queries_redirect(preg_replace('~\bdb=[^&]*&~', '', ME).'db='.urlencode($C), lang(186), rename_database($C, $K['collation']));
            } else {
                $j = explode("\n", str_replace("\r", '', $C));
                $Hh = true;
                $ne = '';
                foreach ($j as $k) {
                    if (count($j) == 1 || $k != '') {
                        if (! create_database($k, $K['collation'])) {
                            $Hh = false;
                        }$ne = $k;
                    }
                }restart_session();
                set_session('dbs', null);
                queries_redirect(ME.'db='.urlencode($ne), lang(187), $Hh);
            }
        } else {
            if (! $K['collation']) {
                redirect(substr(ME, 0, -1));
            }query_redirect('ALTER DATABASE '.idf_escape($C).(preg_match('~^[a-z0-9_]+$~i', $K['collation']) ? " COLLATE $K[collation]" : ''), substr(ME, 0, -1), lang(188));
        }
    }page_header(DB != '' ? lang(66) : lang(114), $n, [], h(DB));
    $ib = collations();
    $C = DB;
    if ($_POST) {
        $C = $K['name'];
    } elseif (DB != '') {
        $K['collation'] = db_collation(DB, $ib);
    } elseif (JUSH == 'sql') {
        foreach (get_vals('SHOW GRANTS') as $nd) {
            if (preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~', $nd, $B) && $B[1]) {
                $C = stripcslashes(idf_unescape("`$B[2]`"));
                break;
            }
        }
    }echo '
<form action="" method="post">
<p>
',($_POST['add_x'] || strpos($C, "\n") ? '<textarea autofocus name="name" rows="10" cols="40">'.h($C).'</textarea><br>' : '<input name="name" autofocus value="'.h($C).'" data-maxlength="64" autocapitalize="off">')."\n".($ib ? html_select('collation', ['' => '('.lang(100).')'] + $ib, $K['collation']).doc_link(['sql' => 'charset-charsets.html', 'mariadb' => 'supported-character-sets-and-collations/', 'mssql' => 'relational-databases/system-functions/sys-fn-helpcollations-transact-sql']) : ''),'<input type="submit" value="',lang(14),'">
';
    if (DB != '') {
        echo "<input type='submit' name='drop' value='".lang(126)."'>".confirm(lang(176, DB))."\n";
    } elseif (! $_POST['add_x'] && $_GET['db'] == '') {
        echo "<input type='image' class='icon' name='add' src='".h(preg_replace('~\\?.*~', '', ME).'?file=plus.gif&version=5.0.6')."' alt='+' title='".lang(107)."'>\n";
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['scheme'])) {
    $K = $_POST;
    if ($_POST && ! $n) {
        $A = preg_replace('~ns=[^&]*&~', '', ME).'ns=';
        if ($_POST['drop']) {
            query_redirect('DROP SCHEMA '.idf_escape($_GET['ns']), $A, lang(189));
        } else {
            $C = trim($K['name']);
            $A .= urlencode($C);
            if ($_GET['ns'] == '') {
                query_redirect('CREATE SCHEMA '.idf_escape($C), $A, lang(190));
            } elseif ($_GET['ns'] != $C) {
                query_redirect('ALTER SCHEMA '.idf_escape($_GET['ns']).' RENAME TO '.idf_escape($C), $A, lang(191));
            } else {
                redirect($A);
            }
        }
    }page_header($_GET['ns'] != '' ? lang(67) : lang(68), $n);
    if (! $K) {
        $K['name'] = $_GET['ns'];
    }echo '
<form action="" method="post">
<p><input name="name" autofocus value="',h($K['name']),'" autocapitalize="off">
<input type="submit" value="',lang(14),'">
';
    if ($_GET['ns'] != '') {
        echo "<input type='submit' name='drop' value='".lang(126)."'>".confirm(lang(176, $_GET['ns']))."\n";
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['call'])) {
    $da = ($_GET['name'] ?: $_GET['call']);
    page_header(lang(192).': '.h($da), $n);
    $Pg = routine($_GET['call'], (isset($_GET['callf']) ? 'FUNCTION' : 'PROCEDURE'));
    $Jd = [];
    $Lf = [];
    foreach ($Pg['fields'] as $u => $o) {
        if (substr($o['inout'], -3) == 'OUT') {
            $Lf[$u] = '@'.idf_escape($o['field']).' AS '.idf_escape($o['field']);
        }if (! $o['inout'] || substr($o['inout'], 0, 2) == 'IN') {
            $Jd[] = $u;
        }
    }if (! $n && $_POST) {
        $Sa = [];
        foreach ($Pg['fields'] as $z => $o) {
            if (in_array($z, $Jd)) {
                $X = process_input($o);
                if ($X === false) {
                    $X = "''";
                }if (isset($Lf[$z])) {
                    $g->query('SET @'.idf_escape($o['field'])." = $X");
                }
            }$Sa[] = (isset($Lf[$z]) ? '@'.idf_escape($o['field']) : $X);
        }$H = (isset($_GET['callf']) ? 'SELECT' : 'CALL').' '.table($da).'('.implode(', ', $Sa).')';
        $Ch = microtime(true);
        $I = $g->multi_query($H);
        $ra = $g->affected_rows;
        echo $b->selectQuery($H, $Ch, ! $I);
        if (! $I) {
            echo "<p class='error'>".error()."\n";
        } else {
            $h = connect($b->credentials());
            if (is_object($h)) {
                $h->select_db(DB);
            }do {
                $I = $g->store_result();
                if (is_object($I)) {
                    select($I, $h);
                } else {
                    echo "<p class='message'>".lang(193, $ra)." <span class='time'>".@date('H:i:s')."</span>\n";
                }
            } while ($g->next_result());
            if ($Lf) {
                select($g->query('SELECT '.implode(', ', $Lf)));
            }
        }
    }echo '
<form action="" method="post">
';
    if ($Jd) {
        echo "<table class='layout'>\n";
        foreach ($Jd as $z) {
            $o = $Pg['fields'][$z];
            $C = $o['field'];
            echo '<tr><th>'.$b->fieldName($o);
            $Y = $_POST['fields'][$C];
            if ($Y != '') {
                if ($o['type'] == 'set') {
                    $Y = implode(',', $Y);
                }
            }input($o, $Y, (string) $_POST['function'][$C]);
            echo "\n";
        }echo "</table>\n";
    }echo '<p>
<input type="submit" value="',lang(192),'">
<input type="hidden" name="token" value="',$mi,'">
</form>

<pre>
';
    function pre_tr($Tg)
    {
        return preg_replace('~^~m', '<tr>', preg_replace('~\|~', '<td>', preg_replace('~\|$~m', '', rtrim($Tg))));
    }$R = '(\+--[-+]+\+\n)';
    $K = '(\| .* \|\n)';
    echo preg_replace_callback("~^$R?$K$R?($K*)$R?~m", function ($B) {
        $Wc = pre_tr($B[2]);

        return "<table>\n".($B[1] ? "<thead>$Wc</thead>\n" : $Wc).pre_tr($B[4])."\n</table>";
    }, preg_replace('~(\n(    -|mysql)&gt; )(.+)~', "\\1<code class='jush-sql'>\\3</code>", preg_replace('~(.+)\n---+\n~', "<b>\\1</b>\n", h($Pg['comment']))));
    echo '</pre>
';
} elseif (isset($_GET['foreign'])) {
    $a = $_GET['foreign'];
    $C = $_GET['name'];
    $K = $_POST;
    if ($_POST && ! $n && ! $_POST['add'] && ! $_POST['change'] && ! $_POST['change-js']) {
        if (! $_POST['drop']) {
            $K['source'] = array_filter($K['source'], 'strlen');
            ksort($K['source']);
            $Wh = [];
            foreach ($K['source'] as $z => $X) {
                $Wh[$z] = $K['target'][$z];
            }$K['target'] = $Wh;
        }if (JUSH == 'sqlite') {
            $I = recreate_table($a, $a, [], [], [" $C" => ($K['drop'] ? '' : ' '.format_foreign_key($K))]);
        } else {
            $c = 'ALTER TABLE '.table($a);
            $I = ($C == '' || queries("$c DROP ".(JUSH == 'sql' ? 'FOREIGN KEY ' : 'CONSTRAINT ').idf_escape($C)));
            if (! $K['drop']) {
                $I = queries("$c ADD".format_foreign_key($K));
            }
        }queries_redirect(ME.'table='.urlencode($a), ($K['drop'] ? lang(194) : ($C != '' ? lang(195) : lang(196))), $I);
        if (! $K['drop']) {
            $n = "$n<br>".lang(197);
        }
    }page_header(lang(198), $n, ['table' => $a], h($a));
    if ($_POST) {
        ksort($K['source']);
        if ($_POST['add']) {
            $K['source'][] = '';
        } elseif ($_POST['change'] || $_POST['change-js']) {
            $K['target'] = [];
        }
    } elseif ($C != '') {
        $ed = foreign_keys($a);
        $K = $ed[$C];
        $K['source'][] = '';
    } else {
        $K['table'] = $a;
        $K['source'] = [''];
    }echo '
<form action="" method="post">
';
    $uh = array_keys(fields($a));
    if ($K['db'] != '') {
        $g->select_db($K['db']);
    }if ($K['ns'] != '') {
        $Hf = get_schema();
        set_schema($K['ns']);
    }$Bg = array_keys(array_filter(table_status('', true), 'Adminer\fk_support'));
    $Wh = array_keys(fields(in_array($K['table'], $Bg) ? $K['table'] : reset($Bg)));
    $sf = "this.form['change-js'].value = '1'; this.form.submit();";
    echo '<p>'.lang(199).': '.html_select('table', $Bg, $K['table'], $sf)."\n";
    if (support('scheme')) {
        $Wg = array_filter($b->schemas(), function ($Vg) {
            return ! preg_match('~^information_schema$~i', $Vg);
        });
        echo lang(75).': '.html_select('ns', $Wg, $K['ns'] != '' ? $K['ns'] : $_GET['ns'], $sf);
        if ($K['ns'] != '') {
            set_schema($Hf);
        }
    } elseif (JUSH != 'sqlite') {
        $Nb = [];
        foreach ($b->databases() as $k) {
            if (! information_schema($k)) {
                $Nb[] = $k;
            }
        }echo lang(74).': '.html_select('db', $Nb, $K['db'] != '' ? $K['db'] : $_GET['db'], $sf);
    }echo '<input type="hidden" name="change-js" value="">
<noscript><p><input type="submit" name="change" value="',lang(200),'"></noscript>
<table>
<thead><tr><th id="label-source">',lang(133),'<th id="label-target">',lang(134),'</thead>
';
    $ce = 0;
    foreach ($K['source'] as $z => $X) {
        echo '<tr>','<td>'.html_select('source['.(+$z).']', [-1 => ''] + $uh, $X, ($ce == count($K['source']) - 1 ? 'foreignAddRow.call(this);' : ''), 'label-source'),'<td>'.html_select('target['.(+$z).']', $Wh, $K['target'][$z], '', 'label-target');
        $ce++;
    }echo '</table>
<p>
',lang(102),': ',html_select('on_delete', [-1 => ''] + explode('|', $m->onActions), $K['on_delete']),' ',lang(101),': ',html_select('on_update', [-1 => ''] + explode('|', $m->onActions), $K['on_update']),doc_link(['sql' => 'innodb-foreign-key-constraints.html', 'mariadb' => 'foreign-keys/', 'pgsql' => 'sql-createtable.html#SQL-CREATETABLE-REFERENCES', 'mssql' => 't-sql/statements/create-table-transact-sql', 'oracle' => 'SQLRF01111']),'<p>
<input type="submit" value="',lang(14),'">
<noscript><p><input type="submit" name="add" value="',lang(201),'"></noscript>
';
    if ($C != '') {
        echo '<input type="submit" name="drop" value="',lang(126),'">',confirm(lang(176, $C));
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['view'])) {
    $a = $_GET['view'];
    $K = $_POST;
    $If = 'VIEW';
    if (JUSH == 'pgsql' && $a != '') {
        $P = table_status($a);
        $If = strtoupper($P['Engine']);
    }if ($_POST && ! $n) {
        $C = trim($K['name']);
        $ya = " AS\n$K[select]";
        $xe = ME.'table='.urlencode($C);
        $Me = lang(202);
        $U = ($_POST['materialized'] ? 'MATERIALIZED VIEW' : 'VIEW');
        if (! $_POST['drop'] && $a == $C && JUSH != 'sqlite' && $U == 'VIEW' && $If == 'VIEW') {
            query_redirect((JUSH == 'mssql' ? 'ALTER' : 'CREATE OR REPLACE').' VIEW '.table($C).$ya, $xe, $Me);
        } else {
            $Yh = $C.'_adminer_'.uniqid();
            drop_create("DROP $If ".table($a), "CREATE $U ".table($C).$ya, "DROP $U ".table($C), "CREATE $U ".table($Yh).$ya, "DROP $U ".table($Yh), ($_POST['drop'] ? substr(ME, 0, -1) : $xe), lang(203), $Me, lang(204), $a, $C);
        }
    }if (! $_POST && $a != '') {
        $K = view($a);
        $K['name'] = $a;
        $K['materialized'] = ($If != 'VIEW');
        if (! $n) {
            $n = error();
        }
    }page_header(($a != '' ? lang(42) : lang(205)), $n, ['table' => $a], h($a));
    echo '
<form action="" method="post">
<p>',lang(184),': <input name="name" value="',h($K['name']),'" data-maxlength="64" autocapitalize="off">
',(support('materializedview') ? ' '.checkbox('materialized', 1, $K['materialized'], lang(128)) : ''),'<p>';
    textarea('select', $K['select']);
    echo '<p>
<input type="submit" value="',lang(14),'">
';
    if ($a != '') {
        echo '<input type="submit" name="drop" value="',lang(126),'">',confirm(lang(176, $a));
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['event'])) {
    $aa = $_GET['event'];
    $Ud = ['YEAR', 'QUARTER', 'MONTH', 'DAY', 'HOUR', 'MINUTE', 'WEEK', 'SECOND', 'YEAR_MONTH', 'DAY_HOUR', 'DAY_MINUTE', 'DAY_SECOND', 'HOUR_MINUTE', 'HOUR_SECOND', 'MINUTE_SECOND'];
    $Dh = ['ENABLED' => 'ENABLE', 'DISABLED' => 'DISABLE', 'SLAVESIDE_DISABLED' => 'DISABLE ON SLAVE'];
    $K = $_POST;
    if ($_POST && ! $n) {
        if ($_POST['drop']) {
            query_redirect('DROP EVENT '.idf_escape($aa), substr(ME, 0, -1), lang(206));
        } elseif (in_array($K['INTERVAL_FIELD'], $Ud) && isset($Dh[$K['STATUS']])) {
            $Ug = "\nON SCHEDULE ".($K['INTERVAL_VALUE'] ? 'EVERY '.q($K['INTERVAL_VALUE'])." $K[INTERVAL_FIELD]".($K['STARTS'] ? ' STARTS '.q($K['STARTS']) : '').($K['ENDS'] ? ' ENDS '.q($K['ENDS']) : '') : 'AT '.q($K['STARTS'])).' ON COMPLETION'.($K['ON_COMPLETION'] ? '' : ' NOT').' PRESERVE';
            queries_redirect(substr(ME, 0, -1), ($aa != '' ? lang(207) : lang(208)), queries(($aa != '' ? 'ALTER EVENT '.idf_escape($aa).$Ug.($aa != $K['EVENT_NAME'] ? "\nRENAME TO ".idf_escape($K['EVENT_NAME']) : '') : 'CREATE EVENT '.idf_escape($K['EVENT_NAME']).$Ug)."\n".$Dh[$K['STATUS']].' COMMENT '.q($K['EVENT_COMMENT']).rtrim(" DO\n$K[EVENT_DEFINITION]", ';').';'));
        }
    }page_header(($aa != '' ? lang(209).': '.h($aa) : lang(210)), $n);
    if (! $K && $aa != '') {
        $L = get_rows('SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = '.q(DB).' AND EVENT_NAME = '.q($aa));
        $K = reset($L);
    }echo '
<form action="" method="post">
<table class="layout">
<tr><th>',lang(184),'<td><input name="EVENT_NAME" value="',h($K['EVENT_NAME']),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(211),'<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">',lang(212),'<td><input name="ENDS" value="',h($K['ENDS']),'">
<tr><th>',lang(213),'<td><input type="number" name="INTERVAL_VALUE" value="',h($K['INTERVAL_VALUE']),'" class="size"> ',html_select('INTERVAL_FIELD', $Ud, $K['INTERVAL_FIELD']),'<tr><th>',lang(117),'<td>',html_select('STATUS', $Dh, $K['STATUS']),'<tr><th>',lang(49),'<td><input name="EVENT_COMMENT" value="',h($K['EVENT_COMMENT']),'" data-maxlength="64">
<tr><th><td>',checkbox('ON_COMPLETION', 'PRESERVE', $K['ON_COMPLETION'] == 'PRESERVE', lang(214)),'</table>
<p>';
    textarea('EVENT_DEFINITION', $K['EVENT_DEFINITION']);
    echo '<p>
<input type="submit" value="',lang(14),'">
';
    if ($aa != '') {
        echo '<input type="submit" name="drop" value="',lang(126),'">',confirm(lang(176, $aa));
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['procedure'])) {
    $da = ($_GET['name'] ?: $_GET['procedure']);
    $Pg = (isset($_GET['function']) ? 'FUNCTION' : 'PROCEDURE');
    $K = $_POST;
    $K['fields'] = (array) $K['fields'];
    if ($_POST && ! process_fields($K['fields']) && ! $n) {
        $Ef = routine($_GET['procedure'], $Pg);
        $Yh = "$K[name]_adminer_".uniqid();
        drop_create("DROP $Pg ".routine_id($da, $Ef), create_routine($Pg, $K), "DROP $Pg ".routine_id($K['name'], $K), create_routine($Pg, ['name' => $Yh] + $K), "DROP $Pg ".routine_id($Yh, $K), substr(ME, 0, -1), lang(215), lang(216), lang(217), $da, $K['name']);
    }page_header(($da != '' ? (isset($_GET['function']) ? lang(218) : lang(219)).': '.h($da) : (isset($_GET['function']) ? lang(220) : lang(221))), $n);
    if (! $_POST && $da != '') {
        $K = routine($_GET['procedure'], $Pg);
        $K['name'] = $da;
    }$ib = get_vals('SHOW CHARACTER SET');
    sort($ib);
    $Qg = routine_languages();
    echo ($ib ? "<datalist id='collations'>".optionlist($ib).'</datalist>' : ''),'
<form action="" method="post" id="form">
<p>',lang(184),': <input name="name" value="',h($K['name']),'" data-maxlength="64" autocapitalize="off">
',($Qg ? lang(19).': '.html_select('language', $Qg, $K['language'])."\n" : ''),'<input type="submit" value="',lang(14),'">
<div class="scrollable">
<table class="nowrap">
';
    edit_fields($K['fields'], $ib, $Pg);
    if (isset($_GET['function'])) {
        echo '<tr><td>'.lang(222);
        edit_type('returns', $K['returns'], $ib, [], (JUSH == 'pgsql' ? ['void', 'trigger'] : []));
    }echo '</table>
',script('editFields();'),'</div>
<p>';
    textarea('definition', $K['definition']);
    echo '<p>
<input type="submit" value="',lang(14),'">
';
    if ($da != '') {
        echo '<input type="submit" name="drop" value="',lang(126),'">',confirm(lang(176, $da));
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['sequence'])) {
    $fa = $_GET['sequence'];
    $K = $_POST;
    if ($_POST && ! $n) {
        $A = substr(ME, 0, -1);
        $C = trim($K['name']);
        if ($_POST['drop']) {
            query_redirect('DROP SEQUENCE '.idf_escape($fa), $A, lang(223));
        } elseif ($fa == '') {
            query_redirect('CREATE SEQUENCE '.idf_escape($C), $A, lang(224));
        } elseif ($fa != $C) {
            query_redirect('ALTER SEQUENCE '.idf_escape($fa).' RENAME TO '.idf_escape($C), $A, lang(225));
        } else {
            redirect($A);
        }
    }page_header($fa != '' ? lang(226).': '.h($fa) : lang(227), $n);
    if (! $K) {
        $K['name'] = $fa;
    }echo '
<form action="" method="post">
<p><input name="name" value="',h($K['name']),'" autocapitalize="off">
<input type="submit" value="',lang(14),'">
';
    if ($fa != '') {
        echo "<input type='submit' name='drop' value='".lang(126)."'>".confirm(lang(176, $fa))."\n";
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['type'])) {
    $ga = $_GET['type'];
    $K = $_POST;
    if ($_POST && ! $n) {
        $A = substr(ME, 0, -1);
        if ($_POST['drop']) {
            query_redirect('DROP TYPE '.idf_escape($ga), $A, lang(228));
        } else {
            query_redirect('CREATE TYPE '.idf_escape(trim($K['name']))." $K[as]", $A, lang(229));
        }
    }page_header($ga != '' ? lang(230).': '.h($ga) : lang(231), $n);
    if (! $K) {
        $K['as'] = 'AS ';
    }echo '
<form action="" method="post">
<p>
';
    if ($ga != '') {
        $Ai = $m->types();
        $xc = type_values($Ai[$ga]);
        if ($xc) {
            echo "<code class='jush-".JUSH."'>ENUM (".h($xc).")</code>\n<p>";
        }echo "<input type='submit' name='drop' value='".lang(126)."'>".confirm(lang(176, $ga))."\n";
    } else {
        echo lang(184).": <input name='name' value='".h($K['name'])."' autocapitalize='off'>\n",doc_link(['pgsql' => 'datatype-enum.html'], '?');
        textarea('as', $K['as']);
        echo "<p><input type='submit' value='".lang(14)."'>\n";
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['check'])) {
    $a = $_GET['check'];
    $C = $_GET['name'];
    $K = $_POST;
    if ($K && ! $n) {
        if (JUSH == 'sqlite') {
            $I = recreate_table($a, $a, [], [], [], 0, [], $C, ($K['drop'] ? '' : $K['clause']));
        } else {
            $I = ($C == '' || queries('ALTER TABLE '.table($a).' DROP CONSTRAINT '.idf_escape($C)));
            if (! $K['drop']) {
                $I = queries('ALTER TABLE '.table($a).' ADD'.($K['name'] != '' ? ' CONSTRAINT '.idf_escape($K['name']) : '')." CHECK ($K[clause])");
            }
        }queries_redirect(ME.'table='.urlencode($a), ($K['drop'] ? lang(232) : ($C != '' ? lang(233) : lang(234))), $I);
    }page_header(($C != '' ? lang(235).': '.h($C) : lang(138)), $n, ['table' => $a]);
    if (! $K) {
        $Za = $m->checkConstraints($a);
        $K = ['name' => $C, 'clause' => $Za[$C]];
    }echo '
<form action="" method="post">
<p>';
    if (JUSH != 'sqlite') {
        echo lang(184).': <input name="name" value="'.h($K['name']).'" data-maxlength="64" autocapitalize="off"> ';
    }echo doc_link(['sql' => 'create-table-check-constraints.html', 'mariadb' => 'constraint/', 'pgsql' => 'ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS', 'mssql' => 'relational-databases/tables/create-check-constraints', 'sqlite' => 'lang_createtable.html#check_constraints'], '?'),'<p>';
    textarea('clause', $K['clause']);
    echo '<p><input type="submit" value="',lang(14),'">
';
    if ($C != '') {
        echo '<input type="submit" name="drop" value="',lang(126),'">',confirm(lang(176, $C));
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['trigger'])) {
    $a = $_GET['trigger'];
    $C = $_GET['name'];
    $xi = trigger_options();
    $K = (array) trigger($C, $a) + ['Trigger' => $a.'_bi'];
    if ($_POST) {
        if (! $n && in_array($_POST['Timing'], $xi['Timing']) && in_array($_POST['Event'], $xi['Event']) && in_array($_POST['Type'], $xi['Type'])) {
            $pf = ' ON '.table($a);
            $cc = 'DROP TRIGGER '.idf_escape($C).(JUSH == 'pgsql' ? $pf : '');
            $xe = ME.'table='.urlencode($a);
            if ($_POST['drop']) {
                query_redirect($cc, $xe, lang(236));
            } else {
                if ($C != '') {
                    queries($cc);
                }queries_redirect($xe, ($C != '' ? lang(237) : lang(238)), queries(create_trigger($pf, $_POST)));
                if ($C != '') {
                    queries(create_trigger($pf, $K + ['Type' => reset($xi['Type'])]));
                }
            }
        }$K = $_POST;
    }page_header(($C != '' ? lang(239).': '.h($C) : lang(240)), $n, ['table' => $a]);
    echo '
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(241),'<td>',html_select('Timing', $xi['Timing'], $K['Timing'], 'triggerChange(/^'.preg_quote($a, '/')."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>',lang(242),'<td>',html_select('Event', $xi['Event'], $K['Event'], "this.form['Timing'].onchange();"),(in_array('UPDATE OF', $xi['Event']) ? " <input name='Of' value='".h($K['Of'])."' class='hidden'>" : ''),'<tr><th>',lang(48),'<td>',html_select('Type', $xi['Type'], $K['Type']),'</table>
<p>',lang(184),': <input name="Trigger" value="',h($K['Trigger']),'" data-maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';
    textarea('Statement', $K['Statement']);
    echo '<p>
<input type="submit" value="',lang(14),'">
';
    if ($C != '') {
        echo '<input type="submit" name="drop" value="',lang(126),'">',confirm(lang(176, $C));
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['user'])) {
    $ha = $_GET['user'];
    $qg = ['' => ['All privileges' => '']];
    foreach (get_rows('SHOW PRIVILEGES') as $K) {
        foreach (explode(',', ($K['Privilege'] == 'Grant option' ? '' : $K['Context'])) as $xb) {
            $qg[$xb][$K['Privilege']] = $K['Comment'];
        }
    }$qg['Server Admin'] += $qg['File access on server'];
    $qg['Databases']['Create routine'] = $qg['Procedures']['Create routine'];
    unset($qg['Procedures']['Create routine']);
    $qg['Columns'] = [];
    foreach (['Select', 'Insert', 'Update', 'References'] as $X) {
        $qg['Columns'][$X] = $qg['Tables'][$X];
    }unset($qg['Server Admin']['Usage']);
    foreach ($qg['Tables'] as $z => $X) {
        unset($qg['Databases'][$z]);
    }$Ze = [];
    if ($_POST) {
        foreach ($_POST['objects'] as $z => $X) {
            $Ze[$X] = (array) $Ze[$X] + (array) $_POST['grants'][$z];
        }
    }$od = [];
    $nf = '';
    if (isset($_GET['host']) && ($I = $g->query('SHOW GRANTS FOR '.q($ha).'@'.q($_GET['host'])))) {
        while ($K = $I->fetch_row()) {
            if (preg_match('~GRANT (.*) ON (.*) TO ~', $K[0], $B) && preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~', $B[1], $De, PREG_SET_ORDER)) {
                foreach ($De as $X) {
                    if ($X[1] != 'USAGE') {
                        $od["$B[2]$X[2]"][$X[1]] = true;
                    }if (preg_match('~ WITH GRANT OPTION~', $K[0])) {
                        $od["$B[2]$X[2]"]['GRANT OPTION'] = true;
                    }
                }
            }if (preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~", $K[0], $B)) {
                $nf = $B[1];
            }
        }
    }if ($_POST && ! $n) {
        $of = (isset($_GET['host']) ? q($ha).'@'.q($_GET['host']) : "''");
        if ($_POST['drop']) {
            query_redirect("DROP USER $of", ME.'privileges=', lang(243));
        } else {
            $bf = q($_POST['user']).'@'.q($_POST['host']);
            $Yf = $_POST['pass'];
            if ($Yf != '' && ! $_POST['hashed'] && ! min_version(8)) {
                $Yf = get_val('SELECT PASSWORD('.q($Yf).')');
                $n = ! $Yf;
            }$Bb = false;
            if (! $n) {
                if ($of != $bf) {
                    $Bb = queries((min_version(5) ? 'CREATE USER' : 'GRANT USAGE ON *.* TO')." $bf IDENTIFIED BY ".(min_version(8) ? '' : 'PASSWORD ').q($Yf));
                    $n = ! $Bb;
                } elseif ($Yf != $nf) {
                    queries("SET PASSWORD FOR $bf = ".q($Yf));
                }
            }if (! $n) {
                $Mg = [];
                foreach ($Ze as $hf => $nd) {
                    if (isset($_GET['grant'])) {
                        $nd = array_filter($nd);
                    }$nd = array_keys($nd);
                    if (isset($_GET['grant'])) {
                        $Mg = array_diff(array_keys(array_filter($Ze[$hf], 'strlen')), $nd);
                    } elseif ($of == $bf) {
                        $lf = array_keys((array) $od[$hf]);
                        $Mg = array_diff($lf, $nd);
                        $nd = array_diff($nd, $lf);
                        unset($od[$hf]);
                    }if (preg_match('~^(.+)\s*(\(.*\))?$~U', $hf, $B) && (! grant('REVOKE', $Mg, $B[2], " ON $B[1] FROM $bf") || ! grant('GRANT', $nd, $B[2], " ON $B[1] TO $bf"))) {
                        $n = true;
                        break;
                    }
                }
            }if (! $n && isset($_GET['host'])) {
                if ($of != $bf) {
                    queries("DROP USER $of");
                } elseif (! isset($_GET['grant'])) {
                    foreach ($od as $hf => $Mg) {
                        if (preg_match('~^(.+)(\(.*\))?$~U', $hf, $B)) {
                            grant('REVOKE', array_keys($Mg), $B[2], " ON $B[1] FROM $bf");
                        }
                    }
                }
            }queries_redirect(ME.'privileges=', (isset($_GET['host']) ? lang(244) : lang(245)), ! $n);
            if ($Bb) {
                $g->query("DROP USER $bf");
            }
        }
    }page_header((isset($_GET['host']) ? lang(34).': '.h("$ha@$_GET[host]") : lang(147)), $n, ['privileges' => ['', lang(70)]]);
    $K = $_POST;
    if ($K) {
        $od = $Ze;
    } else {
        $K = $_GET + ['host' => get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)")];
        $K['pass'] = $nf;
        if ($nf != '') {
            $K['hashed'] = true;
        }$od[(DB == '' || $od ? '' : idf_escape(addcslashes(DB, '%_\\'))).'.*'] = [];
    }echo '<form action="" method="post">
<table class="layout">
<tr><th>',lang(33),'<td><input name="host" data-maxlength="60" value="',h($K['host']),'" autocapitalize="off">
<tr><th>',lang(34),'<td><input name="user" data-maxlength="80" value="',h($K['user']),'" autocapitalize="off">
<tr><th>',lang(35),'<td><input name="pass" id="pass" value="',h($K['pass']),'" autocomplete="new-password">
',($K['hashed'] ? '' : script("typePassword(qs('#pass'));")),(min_version(8) ? '' : checkbox('hashed', 1, $K['hashed'], lang(246), "typePassword(this.form['pass'], this.checked);")),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(70).doc_link(['sql' => 'grant.html#priv_level']);
    $u = 0;
    foreach ($od as $hf => $nd) {
        echo '<th>'.($hf != '*.*' ? "<input name='objects[$u]' value='".h($hf)."' size='10' autocapitalize='off'>" : "<input type='hidden' name='objects[$u]' value='*.*' size='10'>*.*");
        $u++;
    }echo "</thead>\n";
    foreach (['' => '', 'Server Admin' => lang(33), 'Databases' => lang(36), 'Tables' => lang(130), 'Columns' => lang(47), 'Procedures' => lang(247)] as $xb => $Tb) {
        foreach ((array) $qg[$xb] as $pg => $nb) {
            echo '<tr><td'.($Tb ? ">$Tb<td" : " colspan='2'").' lang="en" title="'.h($nb).'">'.h($pg);
            $u = 0;
            foreach ($od as $hf => $nd) {
                $C = "'grants[$u][".h(strtoupper($pg))."]'";
                $Y = $nd[strtoupper($pg)];
                if ($xb == 'Server Admin' && $hf != (isset($od['*.*']) ? '*.*' : '.*')) {
                    echo '<td>';
                } elseif (isset($_GET['grant'])) {
                    echo "<td><select name=$C><option><option value='1'".($Y ? ' selected' : '').'>'.lang(248)."<option value='0'".($Y == '0' ? ' selected' : '').'>'.lang(249).'</select>';
                } else {
                    echo "<td align='center'><label class='block'>","<input type='checkbox' name=$C value='1'".($Y ? ' checked' : '').($pg == 'All privileges' ? " id='grants-$u-all'>" : '>'.($pg == 'Grant option' ? '' : script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$u-all'); };"))),'</label>';
                }$u++;
            }
        }
    }echo "</table>\n",'<p>
<input type="submit" value="',lang(14),'">
';
    if (isset($_GET['host'])) {
        echo '<input type="submit" name="drop" value="',lang(126),'">',confirm(lang(176, "$ha@$_GET[host]"));
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
';
} elseif (isset($_GET['processlist'])) {
    if (support('kill')) {
        if ($_POST && ! $n) {
            $ie = 0;
            foreach ((array) $_POST['kill'] as $X) {
                if (kill_process($X)) {
                    $ie++;
                }
            }queries_redirect(ME.'processlist=', lang(250, $ie), $ie || ! $_POST['kill']);
        }
    }page_header(lang(115), $n);
    echo '
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");
    $u = -1;
    foreach (process_list() as $u => $K) {
        if (! $u) {
            echo "<thead><tr lang='en'>".(support('kill') ? '<th>' : '');
            foreach ($K as $z => $X) {
                echo "<th>$z".doc_link(['sql' => 'show-processlist.html#processlist_'.strtolower($z), 'pgsql' => 'monitoring-stats.html#PG-STAT-ACTIVITY-VIEW', 'oracle' => 'REFRN30223']);
            }echo "</thead>\n";
        }echo '<tr>'.(support('kill') ? '<td>'.checkbox('kill[]', $K[JUSH == 'sql' ? 'Id' : 'pid'], 0) : '');
        foreach ($K as $z => $X) {
            echo '<td>'.((JUSH == 'sql' && $z == 'Info' && preg_match('~Query|Killed~', $K['Command']) && $X != '') || (JUSH == 'pgsql' && $z == 'current_query' && $X != '<IDLE>') || (JUSH == 'oracle' && $z == 'sql_text' && $X != '') ? "<code class='jush-".JUSH."'>".shorten_utf8($X, 100, '</code>').' <a href="'.h(ME.($K['db'] != '' ? 'db='.urlencode($K['db']).'&' : '').'sql='.urlencode($X)).'">'.lang(251).'</a>' : h($X));
        }echo "\n";
    }echo '</table>
</div>
<p>
';
    if (support('kill')) {
        echo ($u + 1).'/'.lang(252, max_connections()),"<p><input type='submit' value='".lang(253)."'>\n";
    }echo '<input type="hidden" name="token" value="',$mi,'">
</form>
',script('tableCheck();');
} elseif (isset($_GET['select'])) {
    $a = $_GET['select'];
    $S = table_status1($a);
    $y = indexes($a);
    $p = fields($a);
    $ed = column_foreign_keys($a);
    $jf = $S['Oid'];
    $qa = get_settings('adminer_import');
    $Ng = [];
    $e = [];
    $Zg = [];
    $Af = [];
    $ci = null;
    foreach ($p as $z => $o) {
        $C = $b->fieldName($o);
        $Xe = html_entity_decode(strip_tags($C), ENT_QUOTES);
        if (isset($o['privileges']['select']) && $C != '') {
            $e[$z] = $Xe;
            if (is_shortable($o)) {
                $ci = $b->selectLengthProcess();
            }
        }if (isset($o['privileges']['where']) && $C != '') {
            $Zg[$z] = $Xe;
        }if (isset($o['privileges']['order']) && $C != '') {
            $Af[$z] = $Xe;
        }$Ng += $o['privileges'];
    }[$M, $pd] = $b->selectColumnsProcess($e, $y);
    $M = array_unique($M);
    $pd = array_unique($pd);
    $Yd = count($pd) < count($M);
    $Z = $b->selectSearchProcess($p, $y);
    $_f = $b->selectOrderProcess($p, $y);
    $_ = $b->selectLimitProcess();
    if ($_GET['val'] && is_ajax()) {
        header('Content-Type: text/plain; charset=utf-8');
        foreach ($_GET['val'] as $Fi => $K) {
            $ya = convert_field($p[key($K)]);
            $M = [$ya ?: idf_escape(key($K))];
            $Z[] = where_check($Fi, $p);
            $J = $m->select($a, $M, $Z, $M);
            if ($J) {
                echo reset($J->fetch_row());
            }
        }exit;
    }$G = $Hi = null;
    foreach ($y as $x) {
        if ($x['type'] == 'PRIMARY') {
            $G = array_flip($x['columns']);
            $Hi = ($M ? $G : []);
            foreach ($Hi as $z => $X) {
                if (in_array(idf_escape($z), $M)) {
                    unset($Hi[$z]);
                }
            }break;
        }
    }if ($jf && ! $G) {
        $G = $Hi = [$jf => 0];
        $y[] = ['type' => 'PRIMARY', 'columns' => [$jf]];
    }if ($_POST && ! $n) {
        $fj = $Z;
        if (! $_POST['all'] && is_array($_POST['check'])) {
            $Za = [];
            foreach ($_POST['check'] as $Va) {
                $Za[] = where_check($Va, $p);
            }$fj[] = '(('.implode(') OR (', $Za).'))';
        }$fj = ($fj ? "\nWHERE ".implode(' AND ', $fj) : '');
        if ($_POST['export']) {
            save_settings(['output' => $_POST['output'], 'format' => $_POST['format']], 'adminer_import');
            dump_headers($a);
            $b->dumpTable($a, '');
            $id = ($M ? implode(', ', $M) : '*').convert_fields($e, $p, $M)."\nFROM ".table($a);
            $rd = ($pd && $Yd ? "\nGROUP BY ".implode(', ', $pd) : '').($_f ? "\nORDER BY ".implode(', ', $_f) : '');
            $H = "SELECT $id$fj$rd";
            if (is_array($_POST['check']) && ! $G) {
                $Di = [];
                foreach ($_POST['check'] as $X) {
                    $Di[] = '(SELECT'.limit($id, "\nWHERE ".($Z ? implode(' AND ', $Z).' AND ' : '').where_check($X, $p).$rd, 1).')';
                }$H = implode(' UNION ALL ', $Di);
            }$b->dumpData($a, 'table', $H);
            $b->dumpFooter();
            exit;
        }if (! $b->selectEmailProcess($Z, $ed)) {
            if ($_POST['save'] || $_POST['delete']) {
                $I = true;
                $ra = 0;
                $O = [];
                if (! $_POST['delete']) {
                    foreach ($_POST['fields'] as $C => $X) {
                        $X = process_input($p[$C]);
                        if ($X !== null && ($_POST['clone'] || $X !== false)) {
                            $O[idf_escape($C)] = ($X !== false ? $X : idf_escape($C));
                        }
                    }
                }if ($_POST['delete'] || $O) {
                    if ($_POST['clone']) {
                        $H = 'INTO '.table($a).' ('.implode(', ', array_keys($O)).")\nSELECT ".implode(', ', $O)."\nFROM ".table($a);
                    }if ($_POST['all'] || ($G && is_array($_POST['check'])) || $Yd) {
                        $I = ($_POST['delete'] ? $m->delete($a, $fj) : ($_POST['clone'] ? queries("INSERT $H$fj") : $m->update($a, $O, $fj)));
                        $ra = $g->affected_rows;
                    } else {
                        foreach ((array) $_POST['check'] as $X) {
                            $ej = "\nWHERE ".($Z ? implode(' AND ', $Z).' AND ' : '').where_check($X, $p);
                            $I = ($_POST['delete'] ? $m->delete($a, $ej, 1) : ($_POST['clone'] ? queries('INSERT'.limit1($a, $H, $ej)) : $m->update($a, $O, $ej, 1)));
                            if (! $I) {
                                break;
                            }$ra += $g->affected_rows;
                        }
                    }
                }$Me = lang(254, $ra);
                if ($_POST['clone'] && $I && $ra == 1) {
                    $oe = last_id();
                    if ($oe) {
                        $Me = lang(169, " $oe");
                    }
                }queries_redirect(remove_from_uri($_POST['all'] && $_POST['delete'] ? 'page' : ''), $Me, $I);
                if (! $_POST['delete']) {
                    $jg = (array) $_POST['fields'];
                    edit_form($a, array_intersect_key($p, $jg), $jg, ! $_POST['clone']);
                    page_footer();
                    exit;
                }
            } elseif (! $_POST['import']) {
                if (! $_POST['val']) {
                    $n = lang(255);
                } else {
                    $I = true;
                    $ra = 0;
                    foreach ($_POST['val'] as $Fi => $K) {
                        $O = [];
                        foreach ($K as $z => $X) {
                            $z = bracket_escape($z, 1);
                            $O[idf_escape($z)] = (preg_match('~char|text~', $p[$z]['type']) || $X != '' ? $b->processInput($p[$z], $X) : 'NULL');
                        }$I = $m->update($a, $O, ' WHERE '.($Z ? implode(' AND ', $Z).' AND ' : '').where_check($Fi, $p), ! $Yd && ! $G, ' ');
                        if (! $I) {
                            break;
                        }$ra += $g->affected_rows;
                    }queries_redirect(remove_from_uri(), lang(254, $ra), $I);
                }
            } elseif (! is_string($Tc = get_file('csv_file', true))) {
                $n = upload_error($Tc);
            } elseif (! preg_match('~~u', $Tc)) {
                $n = lang(256);
            } else {
                save_settings(['output' => $qa['output'], 'format' => $_POST['separator']], 'adminer_import');
                $I = true;
                $jb = array_keys($p);
                preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~', $Tc, $De);
                $ra = count($De[0]);
                $m->begin();
                $fh = ($_POST['separator'] == 'csv' ? ',' : ($_POST['separator'] == 'tsv' ? "\t" : ';'));
                $L = [];
                foreach ($De[0] as $z => $X) {
                    preg_match_all("~((?>\"[^\"]*\")+|[^$fh]*)$fh~", $X.$fh, $Ee);
                    if (! $z && ! array_diff($Ee[1], $jb)) {
                        $jb = $Ee[1];
                        $ra--;
                    } else {
                        $O = [];
                        foreach ($Ee[1] as $u => $fb) {
                            $O[idf_escape($jb[$u])] = ($fb == '' && $p[$jb[$u]]['null'] ? 'NULL' : q(preg_match('~^".*"$~s', $fb) ? str_replace('""', '"', substr($fb, 1, -1)) : $fb));
                        }$L[] = $O;
                    }
                }$I = (! $L || $m->insertUpdate($a, $L, $G));
                if ($I) {
                    $m->commit();
                }queries_redirect(remove_from_uri('page'), lang(257, $ra), $I);
                $m->rollback();
            }
        }
    }$Oh = $b->tableName($S);
    if (is_ajax()) {
        page_headers();
        ob_start();
    } else {
        page_header(lang(52).": $Oh", $n);
    }$O = null;
    if (isset($Ng['insert']) || ! support('table')) {
        $Rf = [];
        foreach ((array) $_GET['where'] as $X) {
            if (isset($ed[$X['col']]) && count($ed[$X['col']]) == 1 && ($X['op'] == '=' || (! $X['op'] && (is_array($X['val']) || ! preg_match('~[_%]~', $X['val']))))) {
                $Rf['set'.'['.bracket_escape($X['col']).']'] = $X['val'];
            }
        }$O = $Rf ? '&'.http_build_query($Rf) : '';
    }$b->selectLinks($S, $O);
    if (! $e && support('table')) {
        echo "<p class='error'>".lang(258).($p ? '.' : ': '.error())."\n";
    } else {
        echo "<form action='' id='form'>\n","<div style='display: none;'>";
        hidden_fields_get();
        echo (DB != '' ? '<input type="hidden" name="db" value="'.h(DB).'">'.(isset($_GET['ns']) ? '<input type="hidden" name="ns" value="'.h($_GET['ns']).'">' : '') : ''),'<input type="hidden" name="select" value="'.h($a).'">',"</div>\n";
        $b->selectColumnsPrint($M, $e);
        $b->selectSearchPrint($Z, $Zg, $y);
        $b->selectOrderPrint($_f, $Af, $y);
        $b->selectLimitPrint($_);
        $b->selectLengthPrint($ci);
        $b->selectActionPrint($y);
        echo "</form>\n";
        $E = $_GET['page'];
        if ($E == 'last') {
            $hd = get_val(count_rows($a, $Z, $Yd, $pd));
            $E = floor(max(0, $hd - 1) / $_);
        }$ah = $M;
        $qd = $pd;
        if (! $ah) {
            $ah[] = '*';
            $yb = convert_fields($e, $p, $M);
            if ($yb) {
                $ah[] = substr($yb, 2);
            }
        }foreach ($M as $z => $X) {
            $o = $p[idf_unescape($X)];
            if ($o && ($ya = convert_field($o))) {
                $ah[$z] = "$ya AS $X";
            }
        }if (! $Yd && $Hi) {
            foreach ($Hi as $z => $X) {
                $ah[] = idf_escape($z);
                if ($qd) {
                    $qd[] = idf_escape($z);
                }
            }
        }$I = $m->select($a, $ah, $Z, $qd, $_f, $_, $E, true);
        if (! $I) {
            echo "<p class='error'>".error()."\n";
        } else {
            if (JUSH == 'mssql' && $E) {
                $I->seek($_ * $E);
            }$qc = [];
            echo "<form action='' method='post' enctype='multipart/form-data'>\n";
            $L = [];
            while ($K = $I->fetch_assoc()) {
                if ($E && JUSH == 'oracle') {
                    unset($K['RNUM']);
                }$L[] = $K;
            }if ($_GET['page'] != 'last' && $_ != '' && $pd && $Yd && JUSH == 'sql') {
                $hd = get_val(' SELECT FOUND_ROWS()');
            }if (! $L) {
                echo "<p class='message'>".lang(12)."\n";
            } else {
                $Ga = $b->backwardKeys($a, $Oh);
                echo "<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),'<thead><tr>'.(! $pd && $M ? '' : "<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);", '')." <a href='".h($_GET['modify'] ? remove_from_uri('modify') : $_SERVER['REQUEST_URI'].'&modify=1')."'>".lang(259).'</a>');
                $Ye = [];
                $kd = [];
                reset($M);
                $zg = 1;
                foreach ($L[0] as $z => $X) {
                    if (! isset($Hi[$z])) {
                        $X = $_GET['columns'][key($M)];
                        $o = $p[$M ? ($X ? $X['col'] : current($M)) : $z];
                        $C = ($o ? $b->fieldName($o, $zg) : ($X['fun'] ? '*' : h($z)));
                        if ($C != '') {
                            $zg++;
                            $Ye[$z] = $C;
                            $d = idf_escape($z);
                            $Dd = remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($z);
                            $Tb = '&desc%5B0%5D=1';
                            $th = isset($o['privileges']['order']);
                            echo "<th id='th[".h(bracket_escape($z))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});", '');
                            $jd = apply_sql_function($X['fun'], $C);
                            echo ($th ? '<a href="'.h($Dd.($_f[0] == $d || $_f[0] == $z || (! $_f && $Yd && $pd[0] == $d) ? $Tb : '')).'">'."$jd</a>" : $jd),"<span class='column hidden'>";
                            if ($th) {
                                echo "<a href='".h($Dd.$Tb)."' title='".lang(58)."' class='text'> ↓</a>";
                            }if (! $X['fun'] && isset($o['privileges']['where'])) {
                                echo '<a href="#fieldset-search" title="'.lang(55).'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($z)."');");
                            }echo '</span>';
                        }$kd[$z] = $X['fun'];
                        next($M);
                    }
                }$ue = [];
                if ($_GET['modify']) {
                    foreach ($L as $K) {
                        foreach ($K as $z => $X) {
                            $ue[$z] = max($ue[$z], min(40, strlen(utf8_decode($X))));
                        }
                    }
                }echo ($Ga ? '<th>'.lang(260) : '')."</thead>\n";
                if (is_ajax()) {
                    ob_end_clean();
                }foreach ($b->rowDescriptions($L, $ed) as $We => $K) {
                    $Ei = unique_array($L[$We], $y);
                    if (! $Ei) {
                        $Ei = [];
                        foreach ($L[$We] as $z => $X) {
                            if (! preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~', $z)) {
                                $Ei[$z] = $X;
                            }
                        }
                    }$Fi = '';
                    foreach ($Ei as $z => $X) {
                        if ((JUSH == 'sql' || JUSH == 'pgsql') && preg_match('~char|text|enum|set~', $p[$z]['type']) && strlen($X) > 64) {
                            $z = (strpos($z, '(') ? $z : idf_escape($z));
                            $z = 'MD5('.(JUSH != 'sql' || preg_match('~^utf8~', $p[$z]['collation']) ? $z : "CONVERT($z USING ".charset($g).')').')';
                            $X = md5($X);
                        }$Fi .= '&'.($X !== null ? urlencode('where['.bracket_escape($z).']').'='.urlencode($X === false ? 'f' : $X) : 'null%5B%5D='.urlencode($z));
                    }echo '<tr>'.(! $pd && $M ? '' : '<td>'.checkbox('check[]', substr($Fi, 1), in_array(substr($Fi, 1), (array) $_POST['check'])).($Yd || information_schema(DB) ? '' : " <a href='".h(ME.'edit='.urlencode($a).$Fi)."' class='edit'>".lang(261).'</a>'));
                    foreach ($K as $z => $X) {
                        if (isset($Ye[$z])) {
                            $o = $p[$z];
                            $X = $m->value($X, $o);
                            if ($X != '' && (! isset($qc[$z]) || $qc[$z] != '')) {
                                $qc[$z] = (is_mail($X) ? $Ye[$z] : '');
                            }$A = '';
                            if (preg_match('~blob|bytea|raw|file~', $o['type']) && $X != '') {
                                $A = ME.'download='.urlencode($a).'&field='.urlencode($z).$Fi;
                            }if (! $A && $X !== null) {
                                foreach ((array) $ed[$z] as $r) {
                                    if (count($ed[$z]) == 1 || end($r['source']) == $z) {
                                        $A = '';
                                        foreach ($r['source'] as $u => $uh) {
                                            $A .= where_link($u, $r['target'][$u], $L[$We][$uh]);
                                        }$A = ($r['db'] != '' ? preg_replace('~([?&]db=)[^&]+~', '\1'.urlencode($r['db']), ME) : ME).'select='.urlencode($r['table']).$A;
                                        if ($r['ns']) {
                                            $A = preg_replace('~([?&]ns=)[^&]+~', '\1'.urlencode($r['ns']), $A);
                                        }if (count($r['source']) == 1) {
                                            break;
                                        }
                                    }
                                }
                            }if ($z == 'COUNT(*)') {
                                $A = ME.'select='.urlencode($a);
                                $u = 0;
                                foreach ((array) $_GET['where'] as $W) {
                                    if (! array_key_exists($W['col'], $Ei)) {
                                        $A .= where_link($u++, $W['col'], $W['val'], $W['op']);
                                    }
                                }foreach ($Ei as $ee => $W) {
                                    $A .= where_link($u++, $ee, $W);
                                }
                            }$X = select_value($X, $A, $o, $ci);
                            $v = h("val[$Fi][".bracket_escape($z).']');
                            $Y = $_POST['val'][$Fi][bracket_escape($z)];
                            $lc = ! is_array($K[$z]) && is_utf8($X) && $L[$We][$z] == $K[$z] && ! $kd[$z] && ! $o['generated'];
                            $ai = preg_match('~text|json|lob~', $o['type']);
                            echo "<td id='$v'".(preg_match(number_type(), $o['type']) && is_numeric(strip_tags($X)) ? " class='number'" : '');
                            if (($_GET['modify'] && $lc) || $Y !== null) {
                                $ud = h($Y !== null ? $Y : $K[$z]);
                                echo '>'.($ai ? "<textarea name='$v' cols='30' rows='".(substr_count($K[$z], "\n") + 1)."'>$ud</textarea>" : "<input name='$v' value='$ud' size='$ue[$z]'>");
                            } else {
                                $ze = strpos($X, '<i>…</i>');
                                echo " data-text='".($ze ? 2 : ($ai ? 1 : 0))."'".($lc ? '' : " data-warning='".h(lang(262))."'").">$X";
                            }
                        }
                    }if ($Ga) {
                        echo '<td>';
                    }$b->backwardKeysPrint($Ga, $L[$We]);
                    echo "</tr>\n";
                }if (is_ajax()) {
                    exit;
                }echo "</table>\n","</div>\n";
            }if (! is_ajax()) {
                if ($L || $E) {
                    $Dc = true;
                    if ($_GET['page'] != 'last') {
                        if ($_ == '' || (count($L) < $_ && ($L || ! $E))) {
                            $hd = ($E ? $E * $_ : 0) + count($L);
                        } elseif (JUSH != 'sql' || ! $Yd) {
                            $hd = ($Yd ? false : found_rows($S, $Z));
                            if ($hd < max(1e4, 2 * ($E + 1) * $_)) {
                                $hd = reset(slow_query(count_rows($a, $Z, $Yd, $pd)));
                            } else {
                                $Dc = false;
                            }
                        }
                    }$Pf = ($_ != '' && ($hd === false || $hd > $_ || $E));
                    if ($Pf) {
                        echo (($hd === false ? count($L) + 1 : $hd - $E * $_) > $_ ? '<p><a href="'.h(remove_from_uri('page').'&page='.($E + 1)).'" class="loadmore">'.lang(263).'</a>'.script("qsl('a').onclick = partial(selectLoadMore, ".(+$_).", '".lang(264)."…');", '') : ''),"\n";
                    }
                }echo "<div class='footer'><div>\n";
                if ($L || $E) {
                    if ($Pf) {
                        $Ge = ($hd === false ? $E + (count($L) >= $_ ? 2 : 1) : floor(($hd - 1) / $_));
                        echo '<fieldset>';
                        if (JUSH != 'simpledb') {
                            echo "<legend><a href='".h(remove_from_uri('page'))."'>".lang(265).'</a></legend>',script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".lang(265)."', '".($E + 1)."')); return false; };"),pagination(0, $E).($E > 5 ? ' …' : '');
                            for ($u = max(1, $E - 4); $u < min($Ge, $E + 5); $u++) {
                                echo pagination($u, $E);
                            }if ($Ge > 0) {
                                echo ($E + 5 < $Ge ? ' …' : ''),($Dc && $hd !== false ? pagination($Ge, $E) : " <a href='".h(remove_from_uri('page').'&page=last')."' title='~$Ge'>".lang(266).'</a>');
                            }
                        } else {
                            echo '<legend>'.lang(265).'</legend>',pagination(0, $E).($E > 1 ? ' …' : ''),($E ? pagination($E, $E) : ''),($Ge > $E ? pagination($E + 1, $E).($Ge > $E + 1 ? ' …' : '') : '');
                        }echo "</fieldset>\n";
                    }echo '<fieldset>','<legend>'.lang(267).'</legend>';
                    $Zb = ($Dc ? '' : '~ ').$hd;
                    $tf = "var checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Zb' : checked); selectCount('selected2', this.checked || !checked ? '$Zb' : checked);";
                    echo checkbox('all', 1, 0, ($hd !== false ? ($Dc ? '' : '~ ').lang(151, $hd) : ''), $tf)."\n","</fieldset>\n";
                    if ($b->selectCommandPrint()) {
                        echo '<fieldset',($_GET['modify'] ? '' : ' class="jsonly"'),'><legend>',lang(259),'</legend><div>
<input type="submit" value="',lang(14),'"',($_GET['modify'] ? '' : ' title="'.lang(255).'"'),'>
</div></fieldset>
<fieldset><legend>',lang(125),' <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="',lang(10),'">
<input type="submit" name="clone" value="',lang(251),'">
<input type="submit" name="delete" value="',lang(18),'">',confirm(),'</div></fieldset>
';
                    }$fd = $b->dumpFormat();
                    foreach ((array) $_GET['columns'] as $d) {
                        if ($d['fun']) {
                            unset($fd['sql']);
                            break;
                        }
                    }if ($fd) {
                        print_fieldset('export', lang(72)." <span id='selected2'></span>");
                        $Mf = $b->dumpOutput();
                        echo ($Mf ? html_select('output', $Mf, $qa['output']).' ' : ''),html_select('format', $fd, $qa['format'])," <input type='submit' name='export' value='".lang(72)."'>\n","</div></fieldset>\n";
                    }$b->selectEmailPrint(array_filter($qc, 'strlen'), $e);
                }echo "</div></div>\n";
                if ($b->selectImportPrint()) {
                    echo '<div>',"<a href='#import'>".lang(71).'</a>',script("qsl('a').onclick = partial(toggle, 'import');", ''),"<span id='import'".($_POST['import'] ? '' : " class='hidden'").'>: ',"<input type='file' name='csv_file'> ",html_select('separator', ['csv' => 'CSV,', 'csv;' => 'CSV;', 'tsv' => 'TSV'], $qa['format'])," <input type='submit' name='import' value='".lang(71)."'>",'</span>','</div>';
                }echo "<input type='hidden' name='token' value='$mi'>\n","</form>\n",(! $pd && $M ? '' : script('tableCheck();'));
            }
        }
    }if (is_ajax()) {
        ob_end_clean();
        exit;
    }
} elseif (isset($_GET['variables'])) {
    $P = isset($_GET['status']);
    page_header($P ? lang(117) : lang(116));
    $Vi = ($P ? show_status() : show_variables());
    if (! $Vi) {
        echo "<p class='message'>".lang(12)."\n";
    } else {
        echo "<table>\n";
        foreach ($Vi as $z => $X) {
            echo '<tr>',"<th><code class='jush-".JUSH.($P ? 'status' : 'set')."'>".h($z).'</code>','<td>'.nl_br(h($X));
        }echo "</table>\n";
    }
} elseif (isset($_GET['script'])) {
    header('Content-Type: text/javascript; charset=utf-8');
    if ($_GET['script'] == 'db') {
        $Kh = ['Data_length' => 0, 'Index_length' => 0, 'Data_free' => 0];
        foreach (table_status() as $C => $S) {
            json_row("Comment-$C", h($S['Comment']));
            if (! is_view($S)) {
                foreach (['Engine', 'Collation'] as $z) {
                    json_row("$z-$C", h($S[$z]));
                }foreach ($Kh + ['Auto_increment' => 0, 'Rows' => 0] as $z => $X) {
                    if ($S[$z] != '') {
                        $X = format_number($S[$z]);
                        if ($X >= 0) {
                            json_row("$z-$C", ($z == 'Rows' && $X && $S['Engine'] == (JUSH == 'pgsql' ? 'table' : 'InnoDB') ? "~ $X" : $X));
                        }if (isset($Kh[$z])) {
                            $Kh[$z] += ($S['Engine'] != 'InnoDB' || $z != 'Data_free' ? $S[$z] : 0);
                        }
                    } elseif (array_key_exists($z, $S)) {
                        json_row("$z-$C", '?');
                    }
                }
            }
        }foreach ($Kh as $z => $X) {
            json_row("sum-$z", format_number($X));
        }json_row('');
    } elseif ($_GET['script'] == 'kill') {
        $g->query('KILL '.number($_POST['kill']));
    } else {
        foreach (count_tables($b->databases()) as $k => $X) {
            json_row("tables-$k", $X);
            json_row("size-$k", db_size($k));
        }json_row('');
    }exit;
} else {
    $Uh = array_merge((array) $_POST['tables'], (array) $_POST['views']);
    if ($Uh && ! $n && ! $_POST['search']) {
        $I = true;
        $Me = '';
        if (JUSH == 'sql' && $_POST['tables'] && count($_POST['tables']) > 1 && ($_POST['drop'] || $_POST['truncate'] || $_POST['copy'])) {
            queries('SET foreign_key_checks = 0');
        }if ($_POST['truncate']) {
            if ($_POST['tables']) {
                $I = truncate_tables($_POST['tables']);
            }$Me = lang(268);
        } elseif ($_POST['move']) {
            $I = move_tables((array) $_POST['tables'],(array) $_POST['views'],$_POST['target']);
            $Me = lang(269);
        } elseif ($_POST['copy']) {
            $I = copy_tables((array) $_POST['tables'],(array) $_POST['views'],$_POST['target']);
            $Me = lang(270);
        } elseif ($_POST['drop']) {
            if ($_POST['views']) {
                $I = drop_views($_POST['views']);
            }if ($I && $_POST['tables']) {
                $I = drop_tables($_POST['tables']);
            }$Me = lang(271);
        } elseif (JUSH == 'sqlite' && $_POST['check']) {
            foreach ((array) $_POST['tables'] as $R) {
                foreach (get_rows('PRAGMA integrity_check('.q($R).')') as $K) {
                    $Me .= '<b>'.h($R).'</b>: '.h($K['integrity_check']).'<br>';
                }
            }
        } elseif (JUSH != 'sql') {
            $I = (JUSH == 'sqlite' ? queries('VACUUM') : apply_queries('VACUUM'.($_POST['optimize'] ? '' : ' ANALYZE'),$_POST['tables']));
            $Me = lang(272);
        } elseif (! $_POST['tables']) {
            $Me = lang(9);
        } elseif ($I = queries(($_POST['optimize'] ? 'OPTIMIZE' : ($_POST['check'] ? 'CHECK' : ($_POST['repair'] ? 'REPAIR' : 'ANALYZE'))).' TABLE '.implode(', ',array_map('Adminer\idf_escape',$_POST['tables'])))) {
            while ($K = $I->fetch_assoc()) {
                $Me .= '<b>'.h($K['Table']).'</b>: '.h($K['Msg_text']).'<br>';
            }
        }queries_redirect(substr(ME,0,-1),$Me,$I);
    }page_header(($_GET['ns'] == '' ? lang(36).': '.h(DB) : lang(75).': '.h($_GET['ns'])),$n,true);
    if ($b->homepage()) {
        if ($_GET['ns'] !== '') {
            echo "<h3 id='tables-views'>".lang(273)."</h3>\n";
            $Th = tables_list();
            if (! $Th) {
                echo "<p class='message'>".lang(9)."\n";
            } else {
                echo "<form action='' method='post'>\n";
                if (support('table')) {
                    echo '<fieldset><legend>'.lang(274)." <span id='selected2'></span></legend><div>","<input type='search' name='query' value='".h($_POST['query'])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');",'')," <input type='submit' name='search' value='".lang(55)."'>\n","</div></fieldset>\n";
                    if ($_POST['search'] && $_POST['query'] != '') {
                        $_GET['where'][0]['op'] = $m->convertOperator('LIKE %%');
                        search_tables();
                    }
                }echo "<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",''),'<th>'.lang(130),'<td>'.lang(275).doc_link(['sql' => 'storage-engines.html']),'<td>'.lang(121).doc_link(['sql' => 'charset-charsets.html', 'mariadb' => 'supported-character-sets-and-collations/']),'<td>'.lang(276).doc_link(['sql' => 'show-table-status.html', 'pgsql' => 'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT', 'oracle' => 'REFRN20286']),'<td>'.lang(277).doc_link(['sql' => 'show-table-status.html', 'pgsql' => 'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT']),'<td>'.lang(278).doc_link(['sql' => 'show-table-status.html']),'<td>'.lang(50).doc_link(['sql' => 'example-auto-increment.html', 'mariadb' => 'auto_increment/']),'<td>'.lang(279).doc_link(['sql' => 'show-table-status.html', 'pgsql' => 'catalog-pg-class.html#CATALOG-PG-CLASS', 'oracle' => 'REFRN20286']),(support('comment') ? '<td>'.lang(49).doc_link(['sql' => 'show-table-status.html', 'pgsql' => 'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE']) : ''),"</thead>\n";
                $T = 0;
                foreach ($Th as $C => $U) {
                    $Yi = ($U !== null && ! preg_match('~table|sequence~i',$U));
                    $v = h('Table-'.$C);
                    echo '<tr><td>'.checkbox(($Yi ? 'views[]' : 'tables[]'),$C,in_array($C,$Uh,true),'','','',$v),'<th>'.(support('table') || support('indexes') ? "<a href='".h(ME).'table='.urlencode($C)."' title='".lang(41)."' id='$v'>".h($C).'</a>' : h($C));
                    if ($Yi) {
                        echo '<td colspan="6"><a href="'.h(ME).'view='.urlencode($C).'" title="'.lang(42).'">'.(preg_match('~materialized~i',$U) ? lang(128) : lang(129)).'</a>','<td align="right"><a href="'.h(ME).'select='.urlencode($C).'" title="'.lang(40).'">?</a>';
                    } else {
                        foreach (['Engine' => [], 'Collation' => [], 'Data_length' => ['create', lang(43)], 'Index_length' => ['indexes', lang(132)], 'Data_free' => ['edit', lang(44)], 'Auto_increment' => ['auto_increment=1&create', lang(43)], 'Rows' => ['select', lang(40)]] as $z => $A) {
                            $v = " id='$z-".h($C)."'";
                            echo $A ? "<td align='right'>".(support('table') || $z == 'Rows' || (support('indexes') && $z != 'Data_length') ? "<a href='".h(ME."$A[0]=").urlencode($C)."'$v title='$A[1]'>?</a>" : "<span$v>?</span>") : "<td id='$z-".h($C)."'>";
                        }$T++;
                    }echo (support('comment') ? "<td id='Comment-".h($C)."'>" : ''),"\n";
                }echo '<tr><td><th>'.lang(252,count($Th)),'<td>'.h(JUSH == 'sql' ? get_val('SELECT @@default_storage_engine') : ''),'<td>'.h(db_collation(DB,collations()));
                foreach (['Data_length', 'Index_length', 'Data_free'] as $z) {
                    echo "<td align='right' id='sum-$z'>";
                }echo "\n","</table>\n","</div>\n";
                if (! information_schema(DB)) {
                    echo "<div class='footer'><div>\n";
                    $Si = "<input type='submit' value='".lang(280)."'> ".on_help("'VACUUM'");
                    $wf = "<input type='submit' name='optimize' value='".lang(281)."'> ".on_help(JUSH == 'sql' ? "'OPTIMIZE TABLE'" : "'VACUUM OPTIMIZE'");
                    echo '<fieldset><legend>'.lang(125)." <span id='selected'></span></legend><div>".(JUSH == 'sqlite' ? $Si."<input type='submit' name='check' value='".lang(282)."'> ".on_help("'PRAGMA integrity_check'") : (JUSH == 'pgsql' ? $Si.$wf : (JUSH == 'sql' ? "<input type='submit' value='".lang(283)."'> ".on_help("'ANALYZE TABLE'").$wf."<input type='submit' name='check' value='".lang(282)."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".lang(284)."'> ".on_help("'REPAIR TABLE'") : '')))."<input type='submit' name='truncate' value='".lang(285)."'> ".on_help(JUSH == 'sqlite' ? "'DELETE'" : "'TRUNCATE".(JUSH == 'pgsql' ? "'" : " TABLE'")).confirm()."<input type='submit' name='drop' value='".lang(126)."'>".on_help("'DROP TABLE'").confirm()."\n";
                    $j = (support('scheme') ? $b->schemas() : $b->databases());
                    if (count($j) != 1 && JUSH != 'sqlite') {
                        $k = (isset($_POST['target']) ? $_POST['target'] : (support('scheme') ? $_GET['ns'] : DB));
                        echo '<p>'.lang(286).': ',($j ? html_select('target',$j,$k) : '<input name="target" value="'.h($k).'" autocapitalize="off">')," <input type='submit' name='move' value='".lang(287)."'>",(support('copy') ? " <input type='submit' name='copy' value='".lang(288)."'> ".checkbox('overwrite',1,$_POST['overwrite'],lang(289)) : ''),"\n";
                    }echo "<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support('table') ? " selectCount('selected2', formChecked(this, /^tables\[/) || $T);" : '').' }'),"<input type='hidden' name='token' value='$mi'>\n","</div></fieldset>\n","</div></div>\n";
                }echo "</form>\n",script('tableCheck();');
            }echo '<p class="links"><a href="'.h(ME).'create=">'.lang(73)."</a>\n",(support('view') ? '<a href="'.h(ME).'view=">'.lang(205)."</a>\n" : '');
            if (support('routine')) {
                echo "<h3 id='routines'>".lang(144)."</h3>\n";
                $Rg = routines();
                if ($Rg) {
                    echo "<table class='odds'>\n",'<thead><tr><th>'.lang(184).'<td>'.lang(48).'<td>'.lang(222)."<td></thead>\n";
                    foreach ($Rg as $K) {
                        $C = ($K['SPECIFIC_NAME'] == $K['ROUTINE_NAME'] ? '' : '&name='.urlencode($K['ROUTINE_NAME']));
                        echo '<tr>','<th><a href="'.h(ME.($K['ROUTINE_TYPE'] != 'PROCEDURE' ? 'callf=' : 'call=').urlencode($K['SPECIFIC_NAME']).$C).'">'.h($K['ROUTINE_NAME']).'</a>','<td>'.h($K['ROUTINE_TYPE']),'<td>'.h($K['DTD_IDENTIFIER']),'<td><a href="'.h(ME.($K['ROUTINE_TYPE'] != 'PROCEDURE' ? 'function=' : 'procedure=').urlencode($K['SPECIFIC_NAME']).$C).'">'.lang(135).'</a>';
                    }echo "</table>\n";
                }echo '<p class="links">'.(support('procedure') ? '<a href="'.h(ME).'procedure=">'.lang(221).'</a>' : '').'<a href="'.h(ME).'function=">'.lang(220)."</a>\n";
            }if (support('sequence')) {
                echo "<h3 id='sequences'>".lang(290)."</h3>\n";
                $ih = get_vals('SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name');
                if ($ih) {
                    echo "<table class='odds'>\n",'<thead><tr><th>'.lang(184)."</thead>\n";
                    foreach ($ih as $X) {
                        echo "<tr><th><a href='".h(ME).'sequence='.urlencode($X)."'>".h($X)."</a>\n";
                    }echo "</table>\n";
                }echo "<p class='links'><a href='".h(ME)."sequence='>".lang(227)."</a>\n";
            }if (support('type')) {
                echo "<h3 id='user-types'>".lang(31)."</h3>\n";
                $Qi = types();
                if ($Qi) {
                    echo "<table class='odds'>\n",'<thead><tr><th>'.lang(184)."</thead>\n";
                    foreach ($Qi as $X) {
                        echo "<tr><th><a href='".h(ME).'type='.urlencode($X)."'>".h($X)."</a>\n";
                    }echo "</table>\n";
                }echo "<p class='links'><a href='".h(ME)."type='>".lang(231)."</a>\n";
            }if (support('event')) {
                echo "<h3 id='events'>".lang(145)."</h3>\n";
                $L = get_rows('SHOW EVENTS');
                if ($L) {
                    echo "<table>\n",'<thead><tr><th>'.lang(184).'<td>'.lang(291).'<td>'.lang(211).'<td>'.lang(212)."<td></thead>\n";
                    foreach ($L as $K) {
                        echo '<tr>','<th>'.h($K['Name']),'<td>'.($K['Execute at'] ? lang(292).'<td>'.$K['Execute at'] : lang(213).' '.$K['Interval value'].' '.$K['Interval field']."<td>$K[Starts]"),"<td>$K[Ends]",'<td><a href="'.h(ME).'event='.urlencode($K['Name']).'">'.lang(135).'</a>';
                    }echo "</table>\n";
                    $Bc = get_val('SELECT @@event_scheduler');
                    if ($Bc && $Bc != 'ON') {
                        echo "<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Bc)."\n";
                    }
                }echo '<p class="links"><a href="'.h(ME).'event=">'.lang(210)."</a>\n";
            }if ($Th) {
                echo script("ajaxSetHtml('".js_escape(ME)."script=db');");
            }
        }
    }
}page_footer();
