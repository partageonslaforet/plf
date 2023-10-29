  
  
  // ************ SEARCH HUNTING DATES ************************************************************
  
function searchHuntingDates(){
                $.ajax({
                type: 'GET',
                url: "assets/inc/php/hunting_dates_search_by_date.php",
                data: "formatDate="+formatDate,
            
                success: function(response){
                   console.log(response);
                    const array = response.split(",");
                    
                     
                   console.log(array);
                    if(response =="pas de chasse (SAISON/TERRITOIRE) pour cette date."){
                        document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                        retour.classList.add('active');
                    }
                    else {
                        huntedTerritories = JSON.parse(response);
                        console.log(huntedTerritories)
                        huntedNber=(huntedTerritories[2].length);
                        console.log(huntedNber)
                       
                       var tab=[]
                       var keys=[]
                       if(huntedNber>0){

                            for(i=0; i<huntedNber; i++){
                               
                            keys = Object.entries(huntedTerritories[2][i])
                            territoriesNbers.push(keys[2][1])
                                 
                            }
                          
                        }
                        
                        var territoriesNber = territoriesNbers.join(',');
                        console.log(territoriesNber)
                        var territoryNber = (territoriesNbers.length);
                        console.log(territoryNber)
                        
                        if(huntedNber>0){
                            document.getElementById("retour").innerHTML = huntedNber + " territoires chassés le "+ formatDate;
                            retour.classList.add('active');
                        }
                       
                       var lyrhuntingterritories = createMultiJson(territoriesNber); 
                    }
                }
            })
}
                