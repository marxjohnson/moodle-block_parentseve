M.block_parentseve = M.block_parentseve || {

    Y: null,

    teachers: null,

    appointment_count: null,

    appointments_deleted: null,

    parentseveid: null,

    altmethod: '',

    init: function(Y, teachers, parentseveid, altmethod) {
        this.Y = Y;
        this.appointment_count = 0;
        this.appointments_deleted = new Array();
        this.teachers = teachers;
        this.parentseveid = parentseveid;
        this.altmethod = altmethod;
        Y.one('#newapp_button').on('click', function(e) {
            e.preventDefault();
            M.block_parentseve.add_appointment();
        });
        Y.one('#parentseve_form').on('submit', function(e) {
            if (!M.block_parentseve.validate(e.target)) {
                e.preventDefault();
            }
        });
    },

    add_appointment: function() {
        Y = this.Y;

        var newapp = Y.Node.create('<div />');
        newapp.set('id', 'parentseve_appointment_'+this.appointment_count);
        newapp.set('className', 'parentseve_appointment');
        var selectname = 'appointmentteacher['+this.appointment_count+']';
        var selectid = 'appointmentteacher_'+this.appointment_count;
        var select = Y.Node.create('<select />');
        select.set('name', selectname);
        select.set('id', selectid);
        select.on('change', function(e) {
            appointment = e.target.get('id').substr(19);
            teacherid = e.target.get('value');
            M.block_parentseve.refresh_schedule(appointment, teacherid);
        });

        var option = Y.Node.create('<option>'+M.util.get_string('selectteacher', 'block_parentseve')+'</option>');
        select.appendChild(option);

        Y.Object.each(this.teachers, function(teacher, key) {
            var option = Y.Node.create('<option value="'+teacher.id+'">'+teacher.firstname+' '+teacher.lastname+'</option>');
            select.appendChild(option);
        });

        var schedule = Y.Node.create('<div class="parentseve_schedule" id="parentseve_schedule_'+this.appointment_count+'" />');
        var cancel = Y.Node.create('<button id="cancel_'+this.appointment_count+'">'+M.util.get_string('cancel', 'moodle')+'</button>');
        cancel.on('click', function(e) {
            e.preventDefault();
            appointment = e.target.get('id').substr(7);
            M.block_parentseve.remove_appointment(appointment);
        });

        newapp.appendChild(select);
        newapp.appendChild(schedule);
        newapp.appendChild(cancel);

        Y.one('#parentseve_appointments').appendChild(newapp);
        this.appointments_deleted[this.appointment_count] = false;
        this.appointment_count++;
    },

    remove_appointment: function(appointment) {
        Y = this.Y;
        var todel = Y.one('#parentseve_appointment_'+appointment);
        var apps = Y.one('#parentseve_appointments');
        apps.removeChild(todel);
        this.appointments_deleted[appointment]=true;
    },

    refresh_schedule: function(appointmentid, teacherid) {
        Y = this.Y;
        var appointment = Y.one('#parentseve_appointment_'+appointmentid);
        Y.io(M.cfg.wwwroot+'/blocks/parentseve/schedule_service.php', {
            method: 'get',
            data: 'parentseveid='+this.parentseveid+'&teacher='+teacherid,
            context: this,
            on: {
                success: function(id, o) {
                    response = Y.JSON.parse(o.responseText);
                    table = Y.Node.create('<table class="parentseve_schedule_form">');
                    Y.Array.each(response.slots, function(timeslot) {
                        row = document.createElement('tr');
                        cell = document.createElement('td');
                        cell.innerHTML = timeslot.displaytime
                        row.appendChild(cell);

                        cell = document.createElement('td');
                        if (timeslot.busy) {
                            cell.innerHTML = M.util.get_string('busy','block_parentseve');
                        } else {
                            cell.innerHTML = '<input type="radio" class="parentseve_app_'+appointmentid+'" name="appointment['+appointmentid+']" value="'+timeslot.time+'" class="parentseve_app">';
                        }
                        row.appendChild(cell);
                        table.appendChild(row);
                    });
                    Y.one('#parentseve_schedule_'+appointmentid).setContent(table);
                },
                failure: function(id, o) {
                    alert(M.util.get_string('formfailed', 'block_parentseve')+' '+this.altmethod);
                }
            }
        });


    },

    validate: function(form) {
        Y = this.Y;

        if (this.appointment_count == 0) {
            alert(M.util.get_string('noappointments', 'block_parentseve'));
            return false;
        }

        var errors = new Array();
        if (form.get('parentname').get('value') == '') {
            errors.push(M.util.get_string('noparentname', 'block_parentseve'));
        }
        if (form.get('studentname').get('value') == '') {
            errors.push(M.util.get_string('nostudentname', 'block_parentseve'));
        }

        for(var i=0,j=this.appointment_count; i<j; i++){
            if (!this.appointments_deleted[i]) {
                time = Y.one('.parentseve_app_'+i+':checked');
                if (!time) {
                    teacherid = Y.one('#appointmentteacher_'+i).get('value');
                    teachername = Y.one('option[value='+teacherid+']').get('text');
                    errors.push(M.util.get_string('noappointmentwith', 'block_parentseve')+' '+teachername);
                }
            }
        }

        if (errors.length) {
            errors.push(M.util.get_string('mustcorrect', 'block_parentseve'));
            errorstring = errors.join('\n');
            alert(errorstring);
            return false;
        }

        return true;

    }
}
