<template>
	<div>
        <div class="text-center" >
            <button type="button" class="close" @click="hideErr">×</button>
            <h5 v-for = "error in errors">{{error}}</h5>
        </div>
        <div class="row">
            
            <div class="col-md-8">
                <table class="table table-striped">
                   <thead>
                       <tr>
                           <th>#</th> 
                           <th>Project_id</th> 
                           <th>Memember_id</th> 
                           <th>role</th> 
                           <th>Action</th>
                       </tr>
                   </thead>
                   <tbody>
                       <tr v-for="assignment in assignments" v-bind:key = "assignment.id">
                            <td>{{assignment.id}}</td>
                            <td>{{assignment.project_id}}</td>
                            <td>{{assignment.member_id}}</td>
                            <td>{{assignment.role}}</td>
                            <td>
                                <button type="button" class="btn btn-danger" @click="deleteAssignment(assignment.id)">Delete</button>
                                <!-- <button type="button" class="btn btn-warning" @click="editProject(project)">Edit</button> -->
                            </td>
                       </tr>
                   </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <h1 class="text-center">Add Form</h1>
                <hr>
                <form @submit.prevent="addAssignment" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="project_id" v-model="assignment.project_id">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="member_id" v-model="assignment.member_id">
                    </div>
                    <div>
                        <label>Type</label>
                        <select v-model="assignment.role">
                            <option value="">select</option>
                            <option value="dev">dev</option>
                            <option value="pl">pl</option>
                            <option value="po">po</option>
                        </select>
                    </div>
                    <button class="button btn-primary">Submit</button>
                </form>
            </div>

        </div>
        
    </div>
</template>

<script type="text/javascript">
    import axios from 'axios';
    export default{
        data(){
            return {
                
                errors:[],
                viewed:false,
                assignments: [],
                assignment: {
                    id: '',
                    project_id: '',
                    member_id: '',
                    role: '',
                },
                assignment_id: '',
                edit: false
            }
        },

        created(){
            this.fetchAssignments();
        },

        methods: {
            fetchAssignments(){
                fetch('/api/assignments')
                    .then(res => res.json())
                    .then(res => {
                        this.assignments = res.data;
                        console.log(this.assignments);
                    })
            },
            deleteAssignment(id){
                if(confirm('Are you sure?')){
                    fetch('/api/assignments/'+id,{
                        method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        if(data.error){
                            this.errors = data;
                            console.log(this.errors);
                        }else{
                            alert('Assignment removed!');
                            this.fetchAssignments();
                        }
                        
                    })
                    .catch(err=>console.log(err));
                }
            },

            async addAssignment(){
                if(this.edit === false){
                    //add
                    const formData = new FormData();
                    formData.append('project_id', this.assignment.project_id)
                    formData.append('member_id', this.assignment.member_id)
                    formData.append('role', this.assignment.role)
                    axios.post('http://testapp2.io/api/assignments',formData )
                    .then(result => {
                        if(result.data.error){
                            this.errors = result.data.error;
                            // console.log(this.addErrors);
                        }else{
                            this.assignment.id = '';
                            this.assignment.project_id = '';
                            this.assignment.member_id = '';
                            this.assignment.role = '';
                            alert('Assignment added!');
                            this.fetchAssignments()
                        }
                      })
                }
                // }else{
                //     //edit
                //     const formData = new FormData();
                //     formData.append('role', this.assignment.role)
                //     formData.append('_method', 'put')
                //     axios.post('http://testapp2.io/api/assignments/'+this.assignment.id,formData )
                //     .then(result => {
                //           if(result.data.error){
                //               this.errors = result.data.error;
                //               // console.log(this.addErrors);
                //           }else{
                //               this.assignment.id = '';
                //               this.assignment.project_id = '';
                //               this.assignment.member_id = '';
                //               this.assignment.role = '';
                //               alert('Project edited!');
                //               this.edit = false;
                //               this.fetchProjects()
                //           }
                //       })
                // }
            },

            // editAssignment(assignment){
            //     this.edit = true;
            //     this.assignment.id = assignment.id;
            //     this.assignment.project_id = assignment.project_id;
            //     this.assignment.member_id = assignment.member_id;
            //     this.assignment.role = assignment.role;
            // },

            hideErr(){
                this.errors = [];
            }
        }
    }



</script>