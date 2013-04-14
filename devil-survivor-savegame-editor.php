<?php
//
// Description:       Devil Survivor savegame editor.
// Contributor(s):    CollosalPokemon, Kyohack
// Last revision:     4/12/2013
//

// Data structure for save file.
function getSave($savegame) {
    // Magic number at beginning of file: 'Devi'.
    $ret['magic'] = substr($savegame, 0x0, 0x4);

    // 2-byte checksum, in little-endian format.
    $ret['checksum'] = substr($savegame, 0x4, 0x2);

    // This segment of data is unknown.
    $ret['unknown1'] = substr($savegame, 0x6, 0x3A);

    // Leader for team 1.
    $ret['l0'] = substr($savegame, 0x40 + (0 * 0x1C), 0x1C);

    // Leader for team 2.
    $ret['l1'] = substr($savegame, 0x40 + (1 * 0x1C), 0x1C);

    // Leader for team 3.
    $ret['l2'] = substr($savegame, 0x40 + (2 * 0x1C), 0x1C);

    // Leader for team 4.
    $ret['l3'] = substr($savegame, 0x40 + (3 * 0x1C), 0x1C);

    // More leaders/demons, that are not currently on a team. These will be available for editing at a later time.
    $ret['unknown2'] = substr($savegame, 0xB0, 0xE0);

    // Demon 1 for team 1.
    $ret['d0'] = substr($savegame, 0x190 + (0 * 0x1A), 0x1A);

    // Demon 2 for team 1.
    $ret['d1'] = substr($savegame, 0x190 + (1 * 0x1A), 0x1A);

    // Demon 1 for team 2.
    $ret['d2'] = substr($savegame, 0x190 + (2 * 0x1A), 0x1A);

    // Demon 2 for team 2.
    $ret['d3'] = substr($savegame, 0x190 + (3 * 0x1A), 0x1A);

    // Demon 1 for team 3.
    $ret['d4'] = substr($savegame, 0x190 + (4 * 0x1A), 0x1A);

    // Demon 2 for team 3.
    $ret['d5'] = substr($savegame, 0x190 + (5 * 0x1A), 0x1A);

    // Demon 1 for team 4.
    $ret['d6'] = substr($savegame, 0x190 + (6 * 0x1A), 0x1A);

    // Demon 2 for team 4.
    $ret['d7'] = substr($savegame, 0x190 + (7 * 0x1A), 0x1A);

    // More demon data. Not sure where it is used in-game.
    $ret['unknown3'] = substr($savegame, 0x260, 0x1A0);

    // Unknown data. Assumed to contain info for leader names.
    $ret['unknown4'] = substr($savegame, 0x400, 0x84);

    // Macca (money).
    $ret['macca'] = substr($savegame, 0x484, 0x4);

    // Shop rating.
    $ret['shopRating'] = substr($savegame, 0x488, 0x4);

    // This segment of data is unknown.
    $ret['unknown5'] = substr($savegame, 0x48C, 0x4A0);

    // Timestamp of some sort. Appears to be in little endian format, and follows a structure similar to epoch timestamps.
    $ret['timestamp'] = substr($savegame, 0x92C, 0x4);

    // This segment of data is unknown.
    $ret['unknown6'] = substr($savegame, 0x930, 0x98);

    // Quick save data. Assumed to contain a similar structure to main save data.
    $ret['quickSave'] = substr($savegame, 0x9C8, 0x1628);

    // Footer at end of file: 'DEVILSURVIVOR_US'.
    $ret['footer'] = substr($savegame, 0x1FF0, 0x10);

    return $ret;
}

// Data structure for leaders and demons.
function getPlayer($player) {
    $ret['team'] = substr($player, 0x0, 0x1);
    $ret['lv'] = substr($player, 0x1, 0x1);
    $ret['speed'] = substr($player, 0x2, 0x1);
    $ret['move'] = substr($player, 0x3, 0x1);
    $ret['exp'] = substr($player, 0x4, 0x2);
    $ret['sr'] = substr($player, 0x6, 0x1);
    $ret['ma'] = substr($player, 0x7, 0x1);
    $ret['vi'] = substr($player, 0x8, 0x1);
    $ret['ag'] = substr($player, 0x9, 0x1);
    $ret['hp'] = substr($player, 0xA, 0x2);
    $ret['mp'] = substr($player, 0xC, 0x2);
    $ret['cs1'] = substr($player, 0xE, 0x1);
    $ret['cs2'] = substr($player, 0xF, 0x1);
    $ret['cs3'] = substr($player, 0x10, 0x1);
    $ret['ps1'] = substr($player, 0x11, 0x1);
    $ret['ps2'] = substr($player, 0x12, 0x1);
    $ret['ps3'] = substr($player, 0x13, 0x1);
    $ret['ar'] = substr($player, 0x14, 0x1);
    $ret['unknown1'] = substr($player, 0x15, 0x1);
    $ret['skillType'] = substr($player, 0x16, 0x1);
    $ret['unknown2'] = substr($player, 0x17, 0x5);

    return $ret;
}

// Output new file, if user clicks save.
if (isset($_POST['save'])) {
    $filename = urldecode($_POST['filename']);
    $savegame = getSave(urldecode($_POST['savegame']));

    // Calculate old checksum.
    $bytes = str_split(implode($savegame), 1);
    $oldSum = 0;
    foreach ($bytes as $byte) {
        $oldSum = $oldSum + ord($byte);
    }

    // Edit macca.
    $savegame['macca'] = pack('V', $_POST['macca']);

    // Edit shop rating.
    $savegame['shopRating'] = pack('V', $_POST['shopRating']);

    // Perform edit operations for all variables for all players on savegame.
    $labels = array('l0', 'l1', 'l2', 'l3', 'd0', 'd1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7');
    foreach ($labels as $label) {
        $player = getPlayer($savegame[$label]);
        $postVars = array('lv', 'speed', 'move', 'exp', 'sr', 'ma', 'vi', 'ag', 'hp', 'mp', 'cs1', 'cs2', 'cs3', 'ps1', 'ps2', 'ps3', 'ar');
        foreach ($postVars as $var) {
            if ($var == 'exp' OR $var == 'hp' OR $var == 'mp') {
                $player[$var] = pack('v', $_POST[$var . '-' . $label]);
            } else {
                $player[$var] = pack('vX', $_POST[$var . '-' . $label]);
            }
        }
        $savegame[$label] = implode($player);
    }

    // Calculate new checksum.
    $bytes = str_split(implode($savegame), 1);
    $newSum = 0;
    foreach ($bytes as $byte) {
        $newSum = $newSum + ord($byte);
    }

    // Checksum can be successfully recalculated for small edits by adding the difference in data. For example, if a variable was incremented by 10, then the same would be done to the checksum.
    $modifiedSum = unpack('v', $savegame['checksum']);
    $modifiedSum = $modifiedSum[1];
    $savegame['checksum'] = pack('v', $modifiedSum + ($newSum - $oldSum));

    // Put the data chunks back together.
    $data = implode($savegame);

    // Output file to client.
    header('Content-type: application/octet-stream');
    header('Expires: Wed, 1 Jan 1997 00:00:00 GMT');
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header("Content-Disposition: attachment; filename=$filename.sav");
    die($data);
} else {
    // CMS integration for PokéCheats
    if (file_exists("../includes/global.php")) {
    $content_type = "search";
    $content_loc = "tools";
    $page_title = "Devil's Survivor savegame editor";
    require("../includes/global.php");
    }

    // Check if savegame has been uploaded.
    if (isset($_POST['upload']) AND strlen(file_get_contents($_FILES['savegame']['tmp_name'])) > 0) {
        $savegame = getSave(file_get_contents($_FILES['savegame']['tmp_name']));
        $savegameSize = strlen(file_get_contents($_FILES['savegame']['tmp_name']));
    }
}

// Names extracted from ROM at 0x16956F.
$names = array(
    'Atsuro',
    'Yuzu',
    'Gin',
    'Kaido',
    'Keisuke',
    'Midori',
    'Haru',
    'Mari',
    'Naoya',
    'Amane',
    'Honda',
    'Atlus',
    'Hero',
    'Mai',
    'Gigolo',
    'Black_Frost',
    'Demon00',
    'Demon01',
    'Demon02',
    'Demon03',
    'Demon04',
    'Demon05',
    'Demon06',
    'Demon07',
    'Demon08',
    'Demon09',
    'Demon10',
    'Demon11',
    'Demon12',
    'Demon13',
    'Demon14',
    'Demon15',
    'Demon16',
    'Demon17',
    'Demon18',
    'Demon19',
    'Demon20',
    'Demon21',
    'Demon22',
    'Demon23',
    'Founder',
    'Azuma',
    'Believer',
    'Fushimi',
    'Izuna',
    'Soldier',
    'Policeman',
    'SDF_Capt.',
    'SDF_Officer',
    'Mobster',
    'Worker',
    'Office_Girl',
    'Man',
    'Woman',
    'Tamer',
    'Shoji',
    'Boy',
    'Old_Woman',
    'Zealot',
    'Sarasvati',
    'Kikuri-Hime',
    'Brigid',
    'Laksmi',
    'Norn',
    'Amaterasu',
    'Thor',
    'Mahakala',
    'Odin',
    'Yama',
    'Shiva',
    'Asura',
    'Orcus',
    'Pazuzu',
    'Abaddon',
    'Tao_Tie',
    'Arioch',
    'Nyalatotep',
    'Makara',
    'Pendragon',
    'Quetzlcotl',
    'Seiryuu',
    'Orochi',
    'Ananta',
    'Toubyou',
    'Basilisk',
    'Ym',
    'Python',
    'Culebre',
    'Angel',
    'Power',
    'Lailah',
    'Remiel',
    'Aniel',
    'Kazfiel',
    'Metatron',
    'Moh_Shuvuu',
    'Suparna',
    'Vidofnir',
    'Badb_Catha',
    'Garuda',
    'Gagyson',
    'Nisroc',
    'Orobas',
    'Decarabia',
    'Agares',
    'Murmur',
    'Heqet',
    'Shiisaa',
    'Bai_Ze',
    'Airavata',
    'Ukano_Mitama',
    'Barong',
    'Kabuso',
    'Hairy_Jack',
    'Nekomata',
    'Cait_Sith',
    'Orthrus',
    'Cerberus',
    'Waira',
    'Garm',
    'Afanc',
    'Mothman',
    'Behemoth',
    'Jambavan',
    'Tlaloc',
    'Heimdall',
    'Hanuman',
    'Cu_Chulainn',
    'Kresnik',
    'Seiten_Taisei',
    'Ganesha',
    'Pixie',
    'Kijimunaa',
    'Jack_Frost',
    'Pyro_Jack',
    'Silky',
    'Lorelei',
    'Vivian',
    'King_Frost',
    'Loki',
    'Hecate',
    'Astaroth',
    'Lucifer',
    'Ubelluris',
    'Nalagiri',
    'Take-Mikazuchi',
    'Zouchou',
    'Koumoku',
    'Jikoku',
    'Bishamon',
    'Kobold',
    'Bilwis',
    'Gozuki',
    'Mezuki',
    'Berserker',
    'Yaksa',
    'Ogre',
    'Ogun',
    'Wendigo',
    'Loa',
    'Legion',
    'Kudlak',
    'Kikimora',
    'Lilim',
    'Yuki_Jyorou',
    'Hariti',
    'Peri',
    'Rangda',
    'Flaemis',
    'Aquans',
    'Aeros',
    'Erthys',
    'Ara_Mitama',
    'Nigi_Mitama',
    'Kusi_Mitama',
    'Saki_Mitama',
    'Ghost_Q',
    'Sage_of_Time',
    'Belzaboul',
    'Belial',
    'Belberith',
    'Babel',
    'Jezebel',
    'Beldr',
    'Abel',
    'Mind_Amane',
    'Sariel',
    'Anael',
    'Cain',
    'Demon',
    'Treasure',
    'Schoolgirl',
    'Maggot',
    'Punk',
    'Tamer_Thug',
    'Tamer_Cop',
    'Tamer_Capt.',
    'SDF_Tamer',
    'SDF_Soldier',
    'Demon_Tamer');

// Auto Skills extracted from ROM at 0x16AC1C.
$autoSkills = array(
    'NONE',
    'Blitzkrieg',
    'Hustle',
    'Fortify',
    'Barrier',
    'Wall',
    'Full Might',
    'Ban Phys',
    'Ban Fire',
    'Ban Ice',
    'Ban Elec',
    'Ban Force',
    'Ban Mystic',
    'Rage Soul',
    'Grace',
    'Marksman',
    'Tailwind',
    'Magic Yin',
    'Battle Aura',
    'Revive',
    'Magic Yang',
    'Healing',
    'Alter Pain',
    'Auto Stop');

// Racial Skills extracted from ROM at 0x16ADB4.
$racialSkills = array(
    'NONE',
    'Affection',
    'Awakening',
    'Chaos Wave',
    'Constrict',
    'Evil Wave',
    'Blood Wine',
    'Flight',
    'Sacrifice',
    'Switch',
    'Animal Leg',
    'Devil Speed',
    'Phantasma',
    'Charm',
    'Tyranny',
    'Double Up',
    'Aggravate',
    'Bind',
    'Devotion',
    'Long Range',
    'Immortal',
    'Evil Flame',
    'Hot Flower',
    'Dark Hand',
    'Violent God',
    "King's Gate",
    "King's Gate",
    'Fiend',
    'Four Devas',
    'Dark Finger');

// Command Skills extracted from ROM at 0x16AFDC.
$commandSkills = array(
    'NONE',
    'Attack',
    'Agi',
    'Agidyne',
    'Maragi',
    'Maragidyne',
    'Bufu',
    'Bufudyne',
    'Mabufu',
    'Mabufudyne',
    'Zio',
    'Ziodyne',
    'Mazio',
    'Maziodyne',
    'Zan',
    'Zandyne',
    'Mazan',
    'Mazandyne',
    'Megido',
    'Megidolaon',
    'Makajamaon',
    'Gigajama',
    'Diajama',
    'Makarakarn',
    'Tetrakarn',
    'Might Call',
    'Shield All',
    'Taunt',
    'Fire Dance',
    'Ice Dance',
    'Elec Dance',
    'Force Dance',
    'Holy Dance',
    'Drain',
    'Judgement',
    'Petra Eyes',
    'Mute Eyes',
    'Paral Eyes',
    'Death Call',
    'Escape',
    'Power Hit',
    'Berserk',
    'Mighty Hit',
    "Devil's Fuge",
    'Purge Light',
    'Anger Hit',
    'Brutal Hit',
    'Hassohappa',
    'Deathbound',
    'Weak Kill',
    'Desperation',
    'Miasma',
    'Lost Flame',
    'Spawn',
    'Sodom Fire',
    'Gunfire',
    'Dia',
    'Diarahan',
    'Media',
    'Mediarahan',
    'Amrita',
    'Prayer',
    'Recarm',
    'Samarecarm',
    'Guard',
    'Confusion',
    'Megidoladyne');

// Passive Skills extracted from ROM at 0x16B448.
$passiveSkills = array(
    'NONE',
    '+Mute',
    '+Poison',
    '+Paralyze',
    '+Stone',
    'Life Bonus',
    'Mana Bonus',
    'Life Surge',
    'Mana Surge',
    'Life',
    'Mana',
    'Hero Aid',
    'Ares Aid',
    'Drain Hit',
    'Attack All',
    'Counter',
    'Revenge',
    'Payback',
    'Fire Jump',
    'Ice Jump',
    'Elec Jump',
    'Force Jump',
    'Anti-Phys',
    'Anti-Fire',
    'Anti-Ice',
    'Anti-Elec',
    'Anti-Force',
    'Anti-Mystic',
    'Anti-Most',
    'Anti-All',
    'Null Phys',
    'Null Fire',
    'Null Ice',
    'Null Elec',
    'Null Force',
    'Null Mystic',
    'Phys Drain',
    'Fire Drain',
    'Ice Drain',
    'Elec Drain',
    'Force Drain',
    'Phys Repel',
    'Fire Repel',
    'Ice Repel',
    'Elec Repel',
    'Force Repel',
    'Watchful',
    'Endure',
    'Life Aid',
    'Life Lift',
    'Mana Aid',
    'Victory Cry',
    'Pierce',
    'Race-O',
    'Race-D',
    'Dual Shadow',
    'Extra One',
    'Knight Soul',
    'Paladin Soul',
    'Hero Soul',
    'Beast Eye',
    'Dragon Eye',
    'Phys Jump',
    'Fire Rise',
    'Ice Rise',
    'Elec Rise',
    'Force Rise',
    'Phys Rise',
    'Leader Soul');

// Output player info.
function showPlayer($player, $label) {
    // Make sure we can use our lists of names.
    global $commandSkills, $passiveSkills, $autoSkills, $racialSkills;

    $player['exp'] = unpack('v', $player['exp']);
    $player['hp'] = unpack('v', $player['hp']);
    $player['mp'] = unpack('v', $player['mp']);

    // Current research findings suggest that if byte 0x16 equals 1, then player is a leader.
    if (ord($player['skillType']) == 0x01) {
        $player['skillType'] = 'Auto';
    } else {
        $player['skillType'] = 'Racial';
    }

    // Output player info in HTML.
    $ret = 'Lv: <input type="text" name="lv-' . $label . '" style="width: 40px;" value="' . ord($player['lv']) . '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    Exp: <input type="text" name="exp-' . $label . '" style="width: 40px;" value="' . $player['exp'][1] . '" /><br />
    <table class="sortable" style="width: 150px;">
        <tbody>
            <tr>
                <td>
                    HP:
                </td>
                <td><input type="text" name="hp-' . $label . '" style="width: 40px;" value="' . $player['hp'][1] . '" /></td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    MP:
                </td>
                <td><input type="text" name="mp-' . $label . '" style="width: 40px;" value="' . $player['mp'][1] . '" /></td>
                <td>
                </td>
            </tr>
            <tr>
                <td>SR:</td>
                <td><input type="text" name="sr-' . $label . '" style="width: 40px;" value="' . ord($player['sr']) . '" /></td>
                <td>
                    <div style="width: ' . ord($player['sr']) . 'px;background-color: #216B84;height: 3px;"></div>
                </td>
            </tr>
            <tr>
                <td>MA:</td>
                <td><input type="text" name="ma-' . $label . '" style="width: 40px;" value="' . ord($player['ma']) . '" /></td>
                <td>
                    <div style="width: ' . ord($player['ma']) . 'px;background-color: #216B84;height: 3px;"></div>
                </td>
            </tr>
            <tr>
                <td>VI:</td>
                <td><input type="text" name="vi-' . $label . '" style="width: 40px;" value="' . ord($player['vi']) . '" /></td>
                <td>
                    <div style="width: ' . ord($player['vi']) . 'px;background-color: #216B84;height: 3px;"></div>
                </td>
            </tr>
            <tr>
                <td>AG:</td>
                <td><input type="text" name="ag-' . $label . '" style="width: 40px;" value="' . ord($player['ag']) . '" /></td>
                <td>
                    <div style="width: ' . ord($player['ag']) . 'px;background-color: #216B84;height: 3px;"></div>
                </td>
            </tr>
        </tbody>
    </table>
    Move: <input type="text" name="move-' . $label . '" style="width: 40px;" value="' . ord($player['move']) . '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Speed: <input type="text" name="speed-' . $label . '" style="width: 40px;" value="' . ord($player['speed']) . '" /><br /><br />
    <table class="sortable">
        <tbody>
            <tr>
                <td>Command 1:</td>
                <td>
                    <select name="cs1-' . $label . '">';
                        foreach ($commandSkills as $key => $val) {
                            $ret .= '<option value="' . $key . '"';
                            if ($key == ord($player['cs1'])) {
                                $ret .= ' selected';
                            }
                            $ret .= '>' . $val . '</option>';
                        }
           $ret .= '</select>
                </td>
            </tr>
            <tr>
                <td>Command 2:</td>
                <td>
                    <select name="cs2-' . $label . '">';
                    foreach ($commandSkills as $key => $val) {
                        $ret .= '<option value="' . $key . '"';
                        if ($key == ord($player['cs2'])) {
                            $ret .= ' selected';
                        }
                        $ret .= '>' . $val . '</option>';
                    }
           $ret .= '</select>
                </td>
            </tr>
            <tr>
                <td>Command 3:</td>
                <td>
                    <select name="cs3-' . $label . '">';
                    foreach ($commandSkills as $key => $val) {
                        $ret .= '<option value="' . $key . '"';
                        if ($key == ord($player['cs3'])) {
                            $ret .= ' selected';
                        }
                        $ret .= '>' . $val . '</option>';
                    }
           $ret .= '</select>
                </td>
            </tr>
            <tr>
                <td>Passive 1:</td>
                <td>
                    <select name="ps1-' . $label . '">';
                    foreach ($passiveSkills as $key => $val) {
                        $ret .= '<option value="' . $key . '"';
                        if ($key == ord($player['ps1'])) {
                            $ret .= ' selected';
                        }
                        $ret .= '>' . $val . '</option>';
                    }
           $ret .= '</select>
                </td>
            </tr>
            <tr>
                <td>Passive 2:</td>
                <td>
                    <select name="ps2-' . $label . '">';
                    foreach ($passiveSkills as $key => $val) {
                        $ret .= '<option value="' . $key . '"';
                        if ($key == ord($player['ps2'])) {
                            $ret .= ' selected';
                        }
                        $ret .= '>' . $val . '</option>';
                    }
           $ret .= '</select>
                </td>
            </tr>
            <tr>
                <td>Passive 3:</td>
                <td>
                    <select name="ps3-' . $label . '">';
                    foreach ($passiveSkills as $key => $val) {
                        $ret .= '<option value="' . $key . '"';
                        if ($key == ord($player['ps3'])) {
                            $ret .= ' selected';
                        }
                        $ret .= '>' . $val . '</option>';
                    }
           $ret .= '</select>
                </td>
            </tr>
            <tr>
                <td>' . $player['skillType'] . ': </td>
                <td>
                    <select name="ar-' . $label . '">';
                    if ($player['skillType'] == 'Auto') {
                        foreach ($autoSkills as $key => $val) {
                            $ret .= '<option value="' . $key . '"';
                            if ($key == ord($player['ar'])) {
                                $ret .= ' selected';
                            }
                            $ret .= '>' . $val . '</option>';
                        }
                    } else {
                        foreach ($racialSkills as $key => $val) {
                            $ret .= '<option value="' . $key . '"';
                            if ($key == ord($player['ar'])) {
                                $ret .= ' selected';
                            }
                            $ret .= '>' . $val . '</option>';
                        }
                    }
           $ret .= '</select>
                </td>
            </tr>
        </tbody>
    </table>';
    return $ret;
}

// Display savegame upload form.
echo "
This save editor for the Devil Survivor DS game currently only supports <b>raw</b> save files. Other formats will be allowed soon.<br /><br />
Please note:<br />
<b>Research on the game's checksum algorithm is incomplete.</b> If you make too many changes to your save file, then the game will report the save as 'corrupt.' Always remember to backup your save file before editing it! If you find any bugs, please report them <a href='http://pokecheats.net/forum/showthread.php?14686-Devil-Survivor-Savegame-Editor'>here</a>.<br /><br />
    <form action=\"devil-survivor-savegame-editor.php\" method=\"post\" enctype=\"multipart/form-data\">
        <input type=\"file\" name=\"savegame\" /><br />
        <input type=\"submit\" name=\"upload\" value=\"Load\" />
    </form>";

// Check if savegame has been uploaded.
if (isset($_POST['upload']) AND $savegameSize > 0) {
    // Verify size.
    if ($savegameSize >= 0x1FFF) {
        // Output save info.
        if ($savegame['magic'] == 'Devi') {
            $savegame['macca'] = unpack('V', $savegame['macca']);
            $savegame['shopRating'] = unpack('V', $savegame['shopRating']);
            echo '
            <form action="devil-survivor-savegame-editor.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="savegame" value="' . urlencode(file_get_contents($_FILES['savegame']['tmp_name'])) . '" />
                <input type="hidden" name="filename" value="' . urlencode($_FILES['savegame']['name']) . '" />
                <input type="submit" name="save" value="Save" />
                <br />
                Macca: <input type="text" name="macca" style="width: 40px;" value="' . $savegame['macca'][1] . '" /><br />
                Shop Rating: <input type="text" name="shopRating" style="width: 40px;" value="' . $savegame['shopRating'][1] . '" />
                <table class="sortable">
                    <tbody>
                        <tr>
                            <td><b>1:</b> </td>
                            <td><b>Demon 1:</b><br />' . showPlayer(getPlayer($savegame['d0']), 'd0') . '</td>
                            <td><b>Leader:</b><br />' . showPlayer(getPlayer($savegame['l0']), 'l0') . '</td>
                            <td><b>Demon 2:</b><br />' . showPlayer(getPlayer($savegame['d1']), 'd1') . '</td>
                        </tr>
                    </tbody>
                </table>
                <table class="sortable">
                    <tbody>
                        <tr>
                            <td><b>2:</b> </td>
                            <td><b>Demon 1:</b><br />' . showPlayer(getPlayer($savegame['d2']), 'd2') . '</td>
                            <td><b>Leader:</b><br />' . showPlayer(getPlayer($savegame['l1']), 'l1') . '</td>
                            <td><b>Demon 2:</b><br />' . showPlayer(getPlayer($savegame['d3']), 'd3') . '</td>
                        </tr>
                    </tbody>
                </table>
                <table class="sortable">
                    <tbody>
                        <tr>
                            <td><b>3:</b> </td>
                            <td><b>Demon 1:</b><br />' . showPlayer(getPlayer($savegame['d4']), 'd4') . '</td>
                            <td><b>Leader:</b><br />' . showPlayer(getPlayer($savegame['l2']), 'l2') . '</td>
                            <td><b>Demon 2:</b><br />' . showPlayer(getPlayer($savegame['d5']), 'd5') . '</td>
                        </tr>
                    </tbody>
                </table>
                <table class="sortable">
                    <tbody>
                        <tr>
                            <td><b>4:</b> </td>
                            <td><b>Demon 1:</b><br />' . showPlayer(getPlayer($savegame['d6']), 'd6') . '</td>
                            <td><b>Leader:</b><br />' . showPlayer(getPlayer($savegame['l3']), 'l3') . '</td>
                            <td><b>Demon 2:</b><br />' . showPlayer(getPlayer($savegame['d7']), 'd7') . '</td>
                        </tr>
                    </tbody>
                </table>
            </form>';
        } else {
            echo 'Savegame is initialized. No data can be read.<br />';
        }
    } else {
        echo 'File size is invalid. Expected a minimum of 8192 bytes, but received ' . $savegameSize . ' instead.<br />';
    }
} else {
    echo 'Please upload a savegame.<br />';
}

// More CMS integration for PokéCheats
if (file_exists("../includes/footer.php")) {
    include "../includes/footer.php";
}
?>