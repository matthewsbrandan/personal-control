    <!--Function-->
    <script>
        function altHref(){
            y = $('#modalCalendarYear').val();
            for(c=0;c<12;c++){
                v = $('.modal-a-calendar')[c].search;
                $('.modal-a-calendar')[c].search=v.substr(0,v.length-4)+y;
            }
        }
    </script>
    <!--Modal Calendar-->
    <button type="button" class="d-none" id="modalCalendarAutoClick" data-toggle="modal" data-target="#modalCalendar"></button>
    <div class="modal fade" tabindex="-1" role="dialog" id="modalCalendar" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="material-icons align-calendar" >date_range</i>
                        <?php echo $mesCalendar[intval(date('m'))].'/'.date('Y');?>
                    </h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body p-2">
                    <div class="mx-3 px-3">
                        <div class="row">
                            <div class="col-12 py-2">
                                
                                <input type="number" id="modalCalendarYear" name="modalCalendarYear" class="form-control" value="<?php echo date('Y'); ?>" onchange="altHref()">
                            </div>
                        </div>                        
                        <?php for($incr=0;$incr<12;$incr+=3){ ?>
                        <div class="row">
                            <?php for($cont=1;$cont<=3;$cont++){ ?>
                            <div class="col-4 p-1">
                                
                                <a href="<?php  echo basename($_SERVER['PHP_SELF']); ?>?calendarM=<?php echo ($cont+$incr); ?>&calendarY=<?php echo date('Y').(basename($_SERVER['PHP_SELF'])=='index.php'?'#divPendente':''); ?>" class="btn btn-block btn-outline-info modal-a-calendar"><?php echo $mesCalendar[$cont+$incr];?></a>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="modal-footer p-3 px-4">
                <a href="<?php  echo basename($_SERVER['PHP_SELF']); ?>" class="btn btn-sm btn-block btn-danger">Mês Atual</a>
                </div>
            </div>
        </div>
    </div>