import {datatableHandleFetchData,datatableHandleEvent} from "/js/helper/datatable.js"

function updateRolePermission(uuid) {
    console.log(uuid)
    const role_uuid = $('#role-uuid').val()
    $ajaxJsonUpdate({
        url : '/role/' + role_uuid + '/permission/update-role',
        data : {
            'permission_uuid' : uuid,
        },
        callback: (res) => {
            Swal.fire({
                icon: 'success',
                title: 'Update Success',
                text: res.message,
            })
        }
    })
}

// Make function globally accessible
window.updateRolePermission = updateRolePermission;

$(function () {
    'use strict';
    //init variable
    const role_uuid = $('#role-uuid')
    const datatable = $('#datatable')

    datatableHandleFetchData({
        html : datatable,
        url : "/role/"+role_uuid.val()+"/permission/grid",
        column : [
        { width: "30%", title: "Permission", data: 'permission' },
        { width: "70%",  title: "Access", data: 'access' },
        ]
    },false,false)

    datatableHandleEvent({
        html: datatable,
        onEvent: 'change',
        classEvent: 'tbody .form-check-input',
        triggerEvent : function (e) {
            e.preventDefault()
             //updateRolePermssion
             $ajaxJsonUpdate({
                url : '/role/' + role_uuid.val() + '/permission/update-role',
                data : {
                    'permission_uuid' : this.id,
                },
                callback: (res) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Update Success',
                        text: res.message,
                    })
                }
            })
        }
    })
})
