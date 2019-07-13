<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Work;
use App\Type;
use App\Student;
use App\Academic;
use App\Career;
use App\CareerStudent;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use Freshwork\ChileanBundle\Rut;

class WorkAcademicsController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        $works = Work::where('status','INGRESADA')->orderBy('id','DESC')->paginate();
        $students = Student::orderBy('id','ASC')->get();
        $academics = Academic::orderBy('id','ASC')->get();
        $types = Type::orderBy('id','ASC')->get();
        //dd($types); //funcionara para revisar los datos de la bd
        return view('admin.worksAcademics.index', compact('works','types','students','academics'));
    }
    public function show($id)
    {
        $work=Work::find($id);
        $types = Type::orderBy('id','ASC')->get();
        $students = Student::orderBy('id','ASC')->get();
        $academics = Academic::orderBy('id','ASC')->get();
        //dd($types); //funcionara para revisar los datos de la bd
        return view('admin.worksAcademics.show',compact('work','types','students','academics'));
    }
    public function edit($id)
    {
        $work=Work::find($id);
        $students = Student::orderBy('id','ASC')->get();
        $academics = Academic::orderBy('id','ASC')->get()->pluck('name');
        $types = Type::orderBy('id','ASC')->get();
        return view('admin.worksAcademics.edit',compact('types','work','students','academics'));
    }
    public function update(Request $request, $id)
    {
        $work=Work::find($id);
        if($request->name1==$request->name2 || $request->name1==$request->name3 || $request->name2==$request->name3){
            return  redirect()->route('worksAcademics.edit',$id)->with('info','No se puede ingresar dos o más veces al mismo académico');
        }

        if($request->name1=="" || $request->name2=="" || $request->name3==""){
            return  redirect()->route('worksAcademics.edit',$id)->with('info','Seleccione 3 académicos');
        }

        if($request->academic_role1==null || $request->academic_role2==null || $request->academic_role3==null){
         return  redirect()->route('worksAcademics.edit',$id)->with('info','No deje espacios vacíos');
        }

        $id1= intval($request->name1)+1; //le sumo 1 para que tome el id del academico , ya que el select parte de 0 
        $id2= intval($request->name2)+1;
        $id3= intval($request->name3)+1;
         
        $work->status = 'ACEPTADA';

        DB::table('academic_work')->insert(
          ['work_id' => $id, 'academic_id'=>$id1,'academic_role' =>$request->academic_role1]
        );

        DB::table('academic_work')->insert(
            ['work_id' => $id, 'academic_id'=>$id2,'academic_role' =>$request->academic_role2]
          );

        DB::table('academic_work')->insert(
            ['work_id' => $id, 'academic_id'=>$id3,'academic_role' =>$request->academic_role3]
        );
        $work->save();
        return  redirect()->route('works.index')->with('info','Actividad de titulación autorizada correctamente');
         
    }
}
