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
                           <th>Name</th> 
                           <th>Information</th> 
                           <th>Deadline</th> 
                           <th>Action</th>
                       </tr>
                   </thead>
                   <tbody>
                       <tr v-for="project in projects" v-bind:key = "project.id">
                            <td>{{project.id}}</td>
                            <td>{{project.name}}</td>
                            <td>{{project.information}}</td>
                            <td>{{project.deadline}}</td>
                            <td>
                                <button type="button" class="btn btn-danger" @click="deleteProject(project.id)">Delete</button>
                                <button type="button" class="btn btn-warning" @click="editProject(project)">Edit</button>
                            </td>
                       </tr>
                   </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <h1 class="text-center">Add + Edit Form</h1>
                <hr>
                <form @submit.prevent="addProject" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="name" v-model="project.name">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="information" v-model="project.information">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="deadline (yyyy-mm-dd)" v-model="project.deadline">
                    </div>
                    <div>
                        <label>Type</label>
                        <select v-model="project.type">
                            <option value="">select</option>
                            <option value="lab">lab</option>
                            <option value="single">single</option>
                            <option value="acceptance">acceptance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select v-model="project.status">
                            <option value="">select</option>
                            <option value="planned">planned</option>
                            <option value="onhold">onhold</option>
                            <option value="doing">doing</option>
                            <option value="done">done</option>
                            <option value="cancelled">cancelled</option>
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
                projects: [],
                project: {
                    id: '',
                    name: '',
                    information: '',
                    deadline: '',
                    type: '',
                    status: '',
                },
                project_id: '',
                edit: false
            }
        },

        created(){
            this.fetchProjects();
        },

        methods: {
            fetchProjects(){
                fetch('/api/projects')
                    .then(res => res.json())
                    .then(res => {
                        this.projects = res.data;
                        console.log(this.projects);
                    })
            },
            deleteProject(id){
                if(confirm('Are you sure?')){
                    fetch('/api/projects/'+id,{
                        method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        if(data.error){
                            this.errors = data;
                            console.log(this.errors);
                        }else{
                            alert('Project removed!');
                            this.fetchProjects();
                        }
                        
                    })
                    .catch(err=>console.log(err));
                }
            },

            async addProject(){
                if(this.edit === false){
                    //add
                    const formData = new FormData();
                    formData.append('name', this.project.name)
                    formData.append('information', this.project.information)
                    formData.append('deadline', this.project.deadline)
                    formData.append('type', this.project.type)
                    formData.append('status', this.project.status)
                    axios.post('http://testapp2.io/api/projects',formData )
                    .then(result => {
                        if(result.data.error){
                            this.errors = result.data.error;
                            // console.log(this.addErrors);
                        }else{
                            this.project.id = '';
                            this.project.name = '';
                            this.project.information = '';
                            this.project.deadline = '';
                            this.project.type = '';
                            this.project.status = '';
                            alert('Article added!');
                            this.fetchProjects()
                        }
                      })
                }else{
                    //edit
                    const formData = new FormData();
                    formData.append('name', this.project.name)
                    formData.append('information', this.project.information)
                    formData.append('deadline', this.project.deadline)
                    formData.append('type', this.project.type)
                    formData.append('status', this.project.status)
                    formData.append('_method', 'put')
                    axios.post('http://testapp2.io/api/projects/'+this.project.id,formData )
                    .then(result => {
                          if(result.data.error){
                              this.errors = result.data.error;
                              // console.log(this.addErrors);
                          }else{
                              this.project.id = '';
                              this.project.name = '';
                              this.project.information = '';
                              this.project.deadline = '';
                              this.project.type = '';
                              this.project.status = '';
                              alert('Project edited!');
                              this.edit = false;
                              this.fetchProjects()
                          }
                      })
                }
            },

            editProject(project){
                this.edit = true;
                this.project.id = project.id;
                this.project.name = project.name;
                this.project.information = project.information;
                this.project.deadline = project.deadline;
                this.project.type = project.type;
                this.project.status = project.status;
                
            },

            hideErr(){
                this.errors = [];
            }
        }
    }



</script>