<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\friend;
use App\profile;
use App\publication;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 

   //hné ena 5arrajt el id ta3 el profile ta3 el user connecté (btw chikoun houa bidou el id ta3 user but for any case of problem or something)
        $friends = Auth::user()->profile()->get('user_id');
        $friends = $friends[0]->user_id;
    //Friend request list
        $request = $this->getFriendRequest($friends);                                          
   //list of friend that send you invitation before and you accepted 
          $listInv=$this->getListFriends('profile_id_to','profileFrom',$friends);
    //list of frind that you send them invitaion and they accepted because they're so Kind <3
          $listAccepted = $this->getListFriends('profile_id_from','profileTo',$friends);
    //Test idha kén wa7da menhom vide or not bech na3rfou ncocatiniw les deux résultat ma3 b3adhhom walla lé
     if(($listAccepted->isEmpty()) == ($listInv->isEmpty()))
     {
         if($listAccepted->isEmpty())
            return view('friends', compact('request'))->with('noFriend',0);
        else
        {   //fsa5t el profileTo 5ater relation manest7a9hech w ena ki chenboukli 3ala zouz ma3 b3adhhom matod5ol b3adhha
                    // $listAccepted = $listAccepted->forget('profileTo');
                    // $listInv = $listInv->forget('profileFrom');

                    // $bothList = $listInv->concat($listAccepted);
                    $both = array();
                    foreach ($listInv as $lista)
                    {
                        array_push($both,['idFriendList' => $lista->id,'id'=>$lista->profileFrom->id , 'fname'=>$lista->profileFrom->fname,
                        'lname' =>  $lista->profileFrom->lname, 'img' => $lista->profileFrom->img,
                        'updated_at' => $lista->updated_at]);
                    
                    }
                    foreach($listAccepted as $lista)
                    {
                        array_push($both,['idFriendList' => $lista->id,'id'=>$lista->profileTo->id, 'fname'=>$lista->profileTo->fname,
                        'lname' => $lista->profileTo->lname, 'img' => $lista->profileTo->img,
                        'updated_at' => $lista->profileTo->updated_at]);
                    }
                    return view('friends',compact('request'))->with('both',$both);
            
        }
     }else
     {
       //mazelt el parti hedhi ya ziw
       if($listInv->isEmpty())
       return view('friends')->with('listA',$listAccepted)->with('request',$request);
       if($listAccepted->isEmpty())
       return view('friends',compact('request'))->with('listI',$listInv);

     }
        
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function show(friend $friend)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function edit(friend $friend)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, friend $friend)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function destroy($friend)
    {
        //
        $frienddel = friend::find($friend);
        $frienddel->delete();
       return redirect()->route('friends.index')->with('friendDown', 'Hakka tfassa5 3chir 3omrek ? ');
    }



    public function getListFriends(String $val1,String $val2,int $friends)
    {
        $list = friend::AreFirends()->where($val1,$friends)->with([$val2 => function($query){
            $query->select('fname','id','lname','img');
        }]);
        $hiddenItems= ['etat','created_at','updated_at','profile_id_to','id','profile_id_from'];
        return $list->get()->makeHidden($hiddenItems);
    }

    //fonction t5arrajlek list ta3 el invi sinon idha mab3athlek 7ad invi trajja3lek false
    public function getFriendRequest(int $friends)
    {
        $list = friend::NonFriends()->where('profile_id_to',$friends)->with(['profileFrom' => function($query){
            $query->select('fname','id','lname','img');
        }])->get();
        
        if($list->isEmpty())
            return false;
        
            return $list;
    }
}
