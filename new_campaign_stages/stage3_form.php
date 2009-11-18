<h2>New Campaign Stage 3 - Post Status </h2>
<h3>Please Select A Post Status</h3>

<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" name="new_campaign3">

    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class='hndle'><span>Post Status:</span></h3>
                    <div class="inside">
                        <table>
                            <tr>
                                <td>Publish</td>
                                <td><input type="radio" name="paideditiononly" value="paideditiononly" id="paideditiononly" disabled="disabled" /></td>
                                <td>&nbsp;Paid Edition Only</td>
                            </tr>     			
                            
                            <tr>
                                <td>Pending</td>
                                <td><input type="radio" name="poststatus" value="pending" id="poststatus_1" /></td>
                                <td></td>
                            </tr>
                                                
                            <tr>
                                <td>Draft </td>
                                <td><input type="radio" name="paideditiononly" value="paideditiononly" id="paideditiononly" disabled="disabled" /></td>
                                <td></td>
                            </tr>
                        </table>                  
                    </div>  
                </div>
            </div>
        </div>
    </div>

    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class='hndle'><span>Other Publish Settings:</span></h3>
                    <div class="inside">
                        <table width="348">
                            <tr>
                                <td width="8"></td>
                                <td width="159">Random Post Date: <input name="paideditiononly" type="checkbox" value="paideditiononly" disabled="disabled" /></td>
                                <td width="165">Paid Edition Only</td>
                            </tr>
                        </table>                  
                    </div>  
                </div>
            </div>
        </div>
    </div>

            <input name="csvfile_columntotal" type="hidden" value="<?php echo $csvfile_columntotal; ?>" />
            <input name="delimiter" type="hidden" value="<?php echo $delimiter; ?>" />
            <input name="stage" type="hidden" value="3" />
            <input name="page" type="hidden" value="new_campaign" />
            <input name="csvfiledirectory" type="hidden" value="<?php echo $csvfiledirectory; ?>" />
            <input name="camid" type="hidden" value="<?php echo $camid; ?>" />
            <input name="layoutstyle" type="hidden" value="<?php echo $layoutstyle; ?>" />
            <input name="statussubmit" class="button-primary" type="submit" value="Submit" />
</form>
    <p>&nbsp;</p>
