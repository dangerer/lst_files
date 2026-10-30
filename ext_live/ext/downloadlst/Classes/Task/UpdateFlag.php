<?php

namespace Hafs\Downloadlst\Task;


class UpdateFlag extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{


    public function execute(): bool
    {
        $this->mangage_new_flag(1, "pages");
        $this->mangage_new_flag(2, "tt_content");
        return TRUE;
    }

    public function mangage_new_flag($contenttyp = 1, $tblname = "pages")
    {
        $time_until_update = 2592000;
        //get new set "new-flags" and insert into log table
        $sql = "SELECT " . $tblname . ".uid FROM " . $tblname . " WHERE new=1 AND uid NOT IN (SELECT uid_foreign FROM tx_downloadlist_newflag WHERE contenttyp=" . $contenttyp . ")";
        $result = $GLOBALS['TYPO3_DB']->sql_query($sql);
        if ($GLOBALS['TYPO3_DB']->sql_num_rows($result) > 0) {
            while ($rs = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
                $data = array("pid" => 0, "tstamp" => time(), "crdate" => time(), "contenttyp" => $contenttyp, "uid_foreign" => $rs["uid"]);
                $GLOBALS['TYPO3_DB']->exec_INSERTquery("tx_downloadlist_newflag", $data);
            }
        }
        //delete new flag which are older then 30 days
        $sql1 = "SELECT uid_foreign FROM tx_downloadlist_newflag WHERE contenttyp=" . $contenttyp . " AND tstamp < " . (time() - $time_until_update);
        $sql = "UPDATE " . $tblname . " SET new=0 WHERE uid IN (" . $sql1 . ")";
        $result = $GLOBALS['TYPO3_DB']->sql_query($sql);
        $sql = "DELETE FROM tx_downloadlist_newflag WHERE contenttyp=" . $contenttyp . " AND tstamp < " . (time() - $time_until_update);
        $result = $GLOBALS['TYPO3_DB']->sql_query($sql);

        //delete log records if new flag is deleted manuell
        $sql1 = "SELECT uid FROM " . $tblname . " WHERE new=1";
        $sql = "DELETE FROM tx_downloadlist_newflag WHERE contenttyp=" . $contenttyp . " AND uid_foreign NOT IN (" . $sql1 . ")";
        $result = $GLOBALS['TYPO3_DB']->sql_query($sql);
    }
}
