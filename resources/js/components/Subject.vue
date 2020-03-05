<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-2">All Subjects</div>
                            <div class="col-md-3">
                                <input type="text" placeholder="Search" @keyup="searchSubject" class="form-control float-left" v-model="search" >
                            </div>
                            <div class="col-md-7">
                                <button class="btn btn-success float-right" @click="openSubjectModal">Add New</button>
                            </div>
                        </div>
                    </div>
                        
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="subject in subjects.data" :key="subject.index">
                                    <td>{{subject.id}}</td>
                                    <td>{{subject.name}}</td>
                                    <td>{{subject.number}}</td>
                                    <td>{{subject.description}}</td>
                                    <td>
                                        <button @click="editSubject(subject)" class="btn btn-warning">Edit</button>
                                        <button @click="deleteSubject(subject.id)" class="btn btn-danger">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">   
                        <pagination :data="subjects" @pagination-change-page="getResults"></pagination>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="subject_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">{{editmode?'Edit Subject':'Add New'}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       <form @submit.prevent="editmode?updateSubject():saveSubject()" >
      <div class="modal-body">
       
    <div class="form-group">
      <label>Name</label>
      <input v-model="form.name" type="text" name="name"
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

    <div class="form-group">
      <label>Numbber</label>
      <input v-model="form.number" type="number" name="number"
        class="form-control" :class="{ 'is-invalid': form.errors.has('number') }">
      <has-error :form="form" field="number"></has-error>
    </div>
    <div class="form-group">
      <label>Description</label>
      
        <textarea v-model="form.description" name="description" class="form-control" :class="{ 'is-invalid': form.errors.has('description') }" id="description" cols="30" rows="5"></textarea>
      <has-error :form="form" field="description"></has-error>
    </div>

    <!-- <button :disabled="form.busy" type="submit" class="btn btn-primary">Submit</button> -->
  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" v-bind:class="get_btn_class()">{{editmode?'Updatte':'Save'}}</button>
      </div>
      </form>
    </div>
  </div>
</div>
    </div>
</template>
<script>
import lodash from 'lodash'
export default {
    data (){
            return {
                search : '',
                editmode : false,
                subjects: {},
                form : new Form({
                    id : '',
                    name : '',
                    number : '',
                    description : ''
                })
            }
        },
    methods : {
        searchSubject: _.debounce(function(){
            Fire.$emit('searching_subject');
            // let searchValue = this.search;
            // console.log(searchValue);
            // if(searchValue !== ""){
            //     axios.get('search_subject?query='+searchValue)
            // .then((res)=>{
            //     console.log(res.data);
            //     this.subjects = res.data;
            // })
            // .catch();
            // }else{
            //     this.getResults();
            // }
        },2000),
        
        openSubjectModal(){
            this.editmode = false;
            this.form.reset();
            $('#subject_modal').modal('show');
        },
        saveSubject(){
            this.$Progress.start()
            // console.log('submitting');
            this.form.post('subjects')
            .then((res)=>{
                $('#subject_modal').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: res.data.message
                })
                Fire.$emit('afterAction');
                this.$Progress.finish()
            });
            
        },
        getResults(page = 1) {
			axios.get('subjects?page=' + page)
				.then(response => {
					this.subjects = response.data;
				});
        },
        editSubject(subject){
            this.editmode = true;
            this.form.fill(subject);
            $('#subject_modal').modal('show');
        },
        get_btn_class(){
            return this.editmode?'btn btn-warning':'btn btn-primary';
        },
        updateSubject(){
            this.$Progress.start()
            this.form.put('subjects/'+this.form.id)
            .then((res)=>{
                Swal.fire(
                'Good job!',
                res.data.message,
                'success'
                )
                this.$Progress.finish();
                Fire.$emit('afterAction');
                $('#subject_modal').modal('hide');
                this.form.reset();
            });
        },
        deleteSubject(id){
            Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.value) {
                axios.delete('subjects/'+id)
                .then((res)=>{
                    console.log(res.data);
                    Swal.fire(
                    res.data.message,
                    res.data,
                    'success'
                    )
                });
                Fire.$emit('afterAction');
                
            }
            })
        }

    },
    mounted(){
        this.getResults();
    },
    created(){
        Fire.$on('searching_subject',()=>{
            let searchValue = this.search;
            // console.log(searchValue);
            if(searchValue !== ""){
                axios.get('search_subject?query='+searchValue)
            .then((res)=>{
                console.log(res.data);
                this.subjects = res.data;
            })
            .catch();
            }else{
                this.getResults();
            }
        });
        Fire.$on('afterAction',()=>{
            this.getResults();
        });
    }
}
</script>
<style scoped>

</style>